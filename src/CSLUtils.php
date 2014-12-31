<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace academicpuma\citeproc\php;

/**
 * Description of CitationStyles
 *
 * @author sebastian
 */
class CSLUtils {
    
    const STYLES_FOLDER = '/styles/';
    
    const PUBLICATIONS_FOLDER = '/pubs/';
    
    static $styles = array(
        'apa',
        //'chicago',
        'din-1505-2',
        'ieee',
        'harvard1',
        'harvard7de',
        //'lncs',
        'vancouver'
        
    );
}
