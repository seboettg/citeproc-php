<?php
/*
 * citeproc-php
 *
 * @link        http://github.com/seboettg/citeproc-php for the source repository
 * @copyright   Copyright (c) 2017 Sebastian Böttger.
 * @license     https://opensource.org/licenses/MIT
 */

namespace Seboettg\CiteProc\Style\Options;

use MyCLabs\Enum\Enum;

/**
 * Class DemoteNonDroppingParticle
 *
 * Sets the display and sorting behavior of the non-dropping-particle in inverted names (e.g. “Koning, W. de”).
 * Some names include a particle that should never be demoted. For these cases the particle should just be included in
 * the family name field, for example for the French general Charles de Gaulle:
 *
 * @package Seboettg\CiteProc\Style
 * @author Sebastian Böttger <seboettg@gmail.com>
 */
class DemoteNonDroppingParticle extends Enum
{
    /**
     * “never”: the non-dropping-particle is treated as part of the family name, whereas the dropping-particle is
     * appended (e.g. “de Koning, W.”, “La Fontaine, Jean de”). The non-dropping-particle is part of the primary sort
     * key (sort order A, e.g. “de Koning, W.” appears under “D”).
     */
    const NEVER = "never";

    /**
     * “sort-only”: same display behavior as “never”, but the non-dropping-particle is demoted to a secondary sort key
     * (sort order B, e.g. “de Koning, W.” appears under “K”).
     */
    const SORT_ONLY = "sort-only";

    /**
     * “display-and-sort” (default): the dropping and non-dropping-particle are appended (e.g. “Koning, W. de” and
     * “Fontaine, Jean de La”). For name sorting, all particles are part of the secondary sort key (sort order B, e.g.
     * “Koning, W. de” appears under “K”).
     */
    const DISPLAY_AND_SORT = "display-and-sort";
}