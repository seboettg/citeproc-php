#!/usr/bin/python -u
from __future__ import print_function

import sys,os,re
from datetime import datetime
from stat import *
import tempfile
try:
    from cStringIO import StringIO
except ImportError:
    from io import StringIO
try:
    from cPickle import Pickler, Unpickler
except ImportError:
    from pickle import Pickler, Unpickler

import subprocess as sub 
import string

try:
    from ConfigParser import ConfigParser
except ImportError:
    from configparser import ConfigParser

try:
    reload(sys)
    sys.setdefaultencoding("utf-8") # Needs Python Unicode build !
except:
    pass

try:
    import json
except:
    import simplejson as json

class ElementMissing(Exception):
    pass

class TooFewArgs(Exception):
    pass

class NoFilesError(Exception):
    pass

class FileNotFound(Exception):
    pass

class MissingHumansFile(Exception):
    pass

class NoLicense(Exception):
    pass

def fixEndings(s):
    s = s.replace('\r\n', '\n')
    s = s.replace('\r', '\n')
    return s


class ApplyLicense:

    def __init__(self):
        self.rex = "(?sm)^^(/\*.*?^\s*\*/\n*)(.*)"
        m = re.match(self.rex, fixEndings(open( os.path.join("src","load.js")).read()) )
        if m:
            self.license = "%s\n" % m.group(1).strip()
        else:
            raise NoLicense
        print(self.license)

    def apply(self):
        for path in [".", "src", os.path.join("tests", "std"), os.path.join("tests","std","humans"),os.path.join("tests","std","bundled"), os.path.join("tests","std","machines"),os.path.join("tests","citeproc-js")]:
            for file in os.listdir( path ):
                if file == "CHANGES.txt" or file == "DESIDERATA.txt":
                    continue
                self.process_file(path,file)

    def process_file(self,path,file):
        filepath = os.path.join( path, file)
        if not filepath.endswith(".js") and not filepath.endswith(".txt") and not filepath.endswith(".json") and not filepath.endswith("README.txt"): return
        text = fixEndings(open(filepath).read())
        oldtext = text
        m = re.match(self.rex,text)
        if m:
            text = "%s\n%s" % (self.license, m.group(2))
        else:
            text = "%s%s" % (self.license, text)
        if text.strip() != oldtext.strip():
            open(filepath,"w+").write(text)

class Params:
    def __init__(self,opt,args,force=None):
        self.opt = opt
        self.args = args
        self.script = os.path.split(sys.argv[0])[1]
        self.pickle = ".".join((os.path.splitext( self.script )[0], "pkl"))
        self.force = force
        self.files = {}
        self.tests = os.path.join(os.getcwd(), "processor-tests")
        self.files['humans'] = {}
        self.files['machines'] = []
        mypath = os.path.split(sys.argv[0])[0]
        self.base = os.path.join( mypath )
        if len(self.base):
            os.chdir(self.base)
        self.initConfig()

    def path(self):
        if self.force:
            return ( os.path.join( self.tests, self.force), )
        else:
            return (os.path.join(self.tests),)

    def getSourcePaths(self):
        if len(self.args) == 2:
            filename = "%s_%s.txt" % tuple(self.args)
            filepath = None
            for path in self.path():
                if os.path.exists( os.path.join(path, "humans", filename)):
                    filepath = (path,os.path.join("humans", filename))
                    break
            if not filepath:
                raise MissingHumansFile(filename,[os.path.join(p,"humans") for p in self.path()])
                self.files['humans'][filename] = (filepath)
        else:
            for path in self.path():
                for filename in os.listdir(os.path.join(path,"humans")):
                    if not filename.endswith(".txt"): continue
                    if args:
                        if not filename.startswith("%s_" % self.args[0]): continue
                    if not self.files['humans'].get(filename):
                        self.files['humans'][filename] = (path,os.path.join("humans",filename))
    
    def clearSource(self):
        for path in self.path():
            mstd = os.path.join(path, "machines")
            for file in os.listdir(mstd):
                if not file.endswith(".json"): continue
                os.unlink(os.path.join(mstd, file))

    def refreshSource(self,force=False):
        groups = {}
        for filename in self.files['humans'].keys():
            hpath = self.files['humans'][filename]
            mpath = os.path.join( self.files['humans'][filename][0], "machines", "%s.json" % filename[:-4] )
            hp = os.path.sep.join( hpath )
            mp = os.path.join( mpath )
            #if force:
            #    self.grindFile(hpath,filename,mp)
            if not os.path.exists( mp ):
                self.grindFile(hpath,filename,mp)
                if self.opt.verbose:
                    print("Created: %s" % mp)
            hmod = os.stat(hp)[ST_MTIME]
            mmod = os.stat(mp)[ST_MTIME]
            if hmod > mmod:
                if self.opt.verbose:
                    print("Old: %s" % mp)
                self.grindFile(hpath,filename,mp)
            m = re.match("([a-z]*)_.*",filename)
            if m:
                groupkey = m.group(1)
                if not groups.get(groupkey):
                    groups[groupkey] = {"mtime":0,"tests":[]}
                groups[groupkey]["tests"].append(filename)
                if hmod > groups[groupkey]["mtime"]:
                    groups[groupkey]["mtime"] = mmod

    def grindFile(self,hpath,filename,mp):
        if self.opt.verbose:
            sys.stdout.write(".")
        test = CslTest(opt,self.cp,hpath,filename)
        test.parse()
        test.repair()
        test.dump(mp)

    def validateSource(self):
        skip_to_pos = 0
        if os.path.exists( self.pickle ):
            upfh = open(self.pickle, 'rb')
            unpickler = Unpickler(upfh)
            old_opt,old_pos = unpickler.load()
            if self.opt == old_opt:
                skip_to_pos = old_pos
        pos = -1
        for filename in sorted(self.files['humans']):
            pos += 1
            if pos < skip_to_pos: continue
            p = self.files['humans'][filename]
            test = CslTest(opt,self.cp,p,filename,pos=pos)
            test.parse()
            test.validate()
        if os.path.exists( self.pickle ):
            os.unlink(self.pickle)

    def initConfig(self):

        for path in self.path():
            if not os.path.exists(os.path.join(path, "machines")):
                os.makedirs(os.path.join(path, "machines"))

        if not os.path.exists( os.path.join("config") ):
            os.makedirs( os.path.join("config") )

        if not os.path.exists( os.path.join("config", "processor.cnf") ):
            test_template = '''[jing]
command: java -jar
path: ../jing/bin/jing.jar

[csl]
v1.0: ../citeproc-js/csl/1.0/csl.rnc
'''
            ofh = open( os.path.join("config", "processor.cnf"), "w+" )
            ofh.write(test_template)
            ofh.close()
        self.cp = ConfigParser()
        self.cp.read(os.path.join("config", "processor.cnf"))

class CslTest:
    def __init__(self,opt,cp,hpath,testname,pos=0):
        self.opt = opt
        self.cp = cp
        self.pos = pos
        self.testname = testname
        self.hpath = hpath
        self.hp = os.path.sep.join( hpath )
        self.CREATORS = ["author","editor","translator","recipient","interviewer"]
        self.CREATORS += ["composer","original-author","container-author","collection-editor"]
        self.RE_ELEMENT = '(?sm)^(.*>>=.*%s[^\n]+)(.*)(\n<<=.*%s.*)'
        self.RE_FILENAME = '^[a-z]+_[a-zA-Z0-9]+\.txt$'
        self.script = os.path.split(sys.argv[0])[1]
        self.pickle = ".".join((os.path.splitext( self.script )[0], "pkl"))
        self.data = {}
        self.raw = fixEndings(open( os.path.sep.join(hpath)).read())

    def parse(self):
        for element in ["MODE","CSL"]:
            self.extract(element,required=True,is_json=False)
            if element == "CSL" and self.data['csl'].endswith('.csl'):
                self.data['csl'] = fixEndings(open( os.path.join("styles", self.data['csl'])).read())
        self.extract("RESULT",required=True,is_json=False)
        self.extract("INPUT",required=True,is_json=True)
        self.extract("CITATION-ITEMS",required=False,is_json=True)
        self.extract("CITATIONS",required=False,is_json=True)
        self.extract("BIBENTRIES",required=False,is_json=True)
        self.extract("BIBSECTION",required=False,is_json=True)
        self.extract("ABBREVIATIONS",required=False,is_json=True)

    def extract(self,tag,required=False,is_json=False,rstrip=False):
        m = re.match(self.RE_ELEMENT %(tag,tag),self.raw)
        data = False
        if m:
            if rstrip:
                data = m.group(2).rstrip()
            else:
                data = m.group(2).strip()
        elif required:
            raise ElementMissing(self.script,tag,self.testname)
        if data != False:
            if is_json:
                data = json.loads(data)
            self.data[tag.lower().replace('-','_')] = data
        else:
            self.data[tag.lower().replace('-','_')] = False

    def repair(self):
        self.fix_dates()
        input_str = json.dumps(self.data["input"],indent=4,sort_keys=True,ensure_ascii=False)
        m = re.match(self.RE_ELEMENT % ("INPUT", "INPUT"),self.raw)
        newraw = m.group(1) + "\n" + input_str + m.group(3)
        if self.data["citation_items"]:
            citations_str = json.dumps(self.data["citation_items"],indent=4,sort_keys=True,ensure_ascii=False)
            m = re.match(self.RE_ELEMENT % ("CITATION-ITEMS", "CITATION-ITEMS"),self.raw)
            newraw = m.group(1) + "\n" + citations_str + m.group(3)
        if self.data["citations"]:
            citations_str = json.dumps(self.data["citations"],indent=4,sort_keys=True,ensure_ascii=False)
            m = re.match(self.RE_ELEMENT % ("CITATIONS", "CITATIONS"),self.raw)
            newraw = m.group(1) + "\n" + citations_str + m.group(3)
        if self.data["abbreviations"]:
            abbreviations_str = json.dumps(self.data["abbreviations"],indent=4,sort_keys=True,ensure_ascii=False)
            m = re.match(self.RE_ELEMENT % ("ABBREVIATIONS", "ABBREVIATIONS"),self.raw)
            newraw = m.group(1) + "\n" + abbreviations_str + m.group(3)
        if self.raw != newraw:
            open(self.hp,"w+").write(newraw)

    def fix_dates(self):
        for pos in range(0, len(self.data["input"]),1):
            for k in ["issued", "event-date", "accessed", "container", "original-date"]:
                if self.data["input"][pos].get(k):
                    newdate = []
                    if not self.data["input"][pos][k].get("date-parts"):
                        start = []
                        for e in ["year","month","day"]:
                            if self.data["input"][pos][k].get(e):
                                start.append( self.data["input"][pos][k][e] )
                                self.data["input"][pos][k].pop(e)
                            else:
                                break
                        if start:
                            newdate.append(start)
                        end = []
                        for e in ["year_end","month_end","day_end"]:
                            if self.data["input"][pos][k].get(e):
                                end.append( self.data["input"][pos][k][e] )
                                self.data["input"][pos][k].pop(e)
                            else:
                                break
                        if end:
                            newdate.append(end)
                        self.data["input"][pos][k]["date-parts"] = newdate

    def dump(self, mpath):
        json.dump(self.data, open(mpath,"w+"), indent=4, sort_keys=True, ensure_ascii=False )

    def validate(self):
        if self.opt.verbose:
            print(self.testname)
        if not os.path.exists(self.cp.get("jing", "path")):
            print("Error: jing not found.")
            print("  Looked in: %s" % self.cp.get("jing", "path"))
            sys.exit()
        m = re.match("(?sm).*version=\"([.0-9a-z]+)\".*",self.data["csl"])
        if m:
            rnc_path = os.path.join(self.cp.get("csl", "v%s" % m.group(1)))
        else:
            print("Error: Unable to find CSL version in %s" % self.hp)
            sys.exit()
        tfd,tfilename = tempfile.mkstemp(dir=".")
        os.write(tfd,self.data["csl"].encode('utf8'))
        os.close(tfd)
        
        jfh = os.popen("%s %s -c %s %s" % (self.cp.get("jing", "command"), self.cp.get("jing", "path"),rnc_path,tfilename))
        success = True
        plural = ""
        while 1:
            line = jfh.readline()
            if not line: break
            line = line.strip()
            e = re.match("^fatal:",line)
            if e:
                print(line)
                sys.exit()
            m = re.match(".*:([0-9]+):([0-9]+):  *error:(.*)",line)
            if m:
              if success:
                  print("\n##")
                  print("#### Error%s in CSL for test: %s" % (plural,self.hp))
                  print("##\n")
                  success = False
              print("  %s @ line %s" %(m.group(3).upper(),m.group(1)))
              plural = "s"
        jfh.close()
        os.unlink(tfilename)
        if not success:
            print("")
            io = StringIO()
            io.write(self.data["csl"])
            io.seek(0)
            linepos = 1
            while 1:
                cslline = io.readline()
                if not cslline: break
                cslline = cslline.rstrip()
                print("%3d  %s" % (linepos,cslline))
                linepos += 1
            pfh = open( self.pickle,"wb+")
            pickler = Pickler( pfh )

            pickler.dump( (opt, self.pos) )
            sys.exit()
        
 
if __name__ == "__main__":

    from optparse import OptionParser

    os.environ['LANG'] = "en_US.UTF-8"

    usage = '\n   %prog [options]'

    description="This script."

    parser = OptionParser(usage=usage,description=description,epilog="Happy testing!")
    parser.add_option("-c", "--cranky", dest="cranky",
                      default=False,
                      action="store_true", 
                      help='Attempt to validate style code for testing against the CSL schema.')
    parser.add_option("-g", "--grind", dest="grind",
                      default=False,
                      action="store_true", 
                      help='Force grinding of human-readable test code into machine-readable form.')
    parser.add_option("-v", "--verbose", dest="verbose",
                      default=False,
                      action="store_true", 
                      help='Display test names during processing.')
    (opt, args) = parser.parse_args()

    if not opt.grind and not opt.cranky:
        parser.print_help()
        sys.exit()
    
    # Testing sequence:
    # + Get single tests working
    #   Get automatic grinding for single tests working
    #   Get forced grinding for single tests working
    #   Get forced grinding and testing for single tests working
    #   Get CSL integrity check working for single tests
    #   Check running of all tests
    #   Check grinding of all tests followed by testing
    #   Check CSL integrity check of all tests

    #
    # Set up paths engine
    # 
    params = Params(opt,args)

    try:
        params.getSourcePaths()
        if opt.grind:
            params.clearSource()
            params.refreshSource(force=True)
            print("")
        else:
            params.refreshSource()
        if opt.cranky:
            params.validateSource()
    except (KeyboardInterrupt, SystemExit):
        for file in os.listdir("."):
            if not file.startswith("tmp") or not len(file) == 9: continue
            os.unlink(file)
        sys.exit()
    except MissingHumansFile as error:
        parser.print_help()
        print('''\nError: File \"%s\" not found.
       Looked in:''' % error[0])
        for path in error[1]:
            print('         %s' % path)
    except NoFilesError:
        print('\nError: No files to process!\n')
    except NoLicense:
        print('\nError: No license found in load.js')

    print("Processor tests successfully compiled")
