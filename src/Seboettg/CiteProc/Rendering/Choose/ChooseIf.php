<?php
/*
 * This file is a part of HDS (HeBIS Discovery System). HDS is an 
 * extension of the open source library search engine VuFind, that 
 * allows users to search and browse beyond resources. More 
 * Information about VuFind you will find on http://www.vufind.org
 * 
 * Copyright (C) 2016 
 * HeBIS Verbundzentrale des HeBIS-Verbundes 
 * Goethe-Universität Frankfurt / Goethe University of Frankfurt
 * http://www.hebis.de
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

namespace Seboettg\CiteProc\Rendering\Choose;
use Seboettg\CiteProc\Constraint\ConstraintInterface;
use Seboettg\CiteProc\Constraint\Factory;
use Seboettg\CiteProc\Rendering\RenderingInterface;
use Seboettg\Collection\ArrayList;


/**
 * Class ChooseIf
 * @package Seboettg\CiteProc\Node\Choose
 *
 * @author Sebastian Böttger <boettger@hebis.uni-frankfurt.de>
 */
class ChooseIf implements RenderingInterface
{

    /**
     * @var ConstraintInterface
     */
    private $constraint;

    /**
     * @var ArrayList
     */
    protected $children;

    private $match;

    public function __construct(\SimpleXMLElement $node)
    {
        $this->children = new ArrayList();

        $this->match = (string) $node['match'];

        foreach ($node->attributes() as $name => $value) {
            if ('match' !== $name) {
                $this->constraint = Factory::createConstraint((string) $name, (string) $value, $this->match);
            }
        }

        foreach ($node->children() as $child) {
            $this->children->append(Factory::create($child));
        }
    }

    public function render($data)
    {
        $ret = "";
        /** @var RenderingInterface $child */
        foreach ($this->children as $child) {
            $ret .= $child->render($data);
        }
        return $ret;
    }

    public function match($data)
    {
        if (isset($this->constraint)) {
            return $this->constraint->validate($data);
        }

        return false;
    }
}
