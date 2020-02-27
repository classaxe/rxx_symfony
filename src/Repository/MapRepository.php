<?php

namespace App\Repository;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MapRepository
{
    private $colors;
    private $image;
    private $images;
    private $projectDir;
    private $imageDir;

    const MAPS = [
        'af' => [
            'mode' =>           'African NDB List approved Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'af',
            'map' =>            'af_map.gif',
            'shortName' =>      'Africa',
        ],
        'alaska' => [
            'mode' =>           'Beacons in Alaska',
            'map' =>            'map_alaska_beacons.gif',
            'text' =>           'OR... try the <a href="state_map/?simple=1&SP=AK">interactive map of Alaska</a>',
            'shortName' =>      'Alaska',
        ],
        'as' => [
            'mode' =>           'Asian NDB List Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'map' =>            'as_map.gif',
            'shortName' =>      'Asia',
        ],
        'au' => [
            'mode' =>           'Australian NDB List Country Codes',
            'stateBtn' =>       'Territories',
            'stateFilter' =>    'aus',
            'map' =>            'au_map.gif',
            'shortName' =>      'Australia',
        ],
        'eu' => [
            'mode' =>           'European NDB List Country Codes',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'eu',
            'map' =>            'eu_map.gif',
            'shortName' =>      'Europe',
        ],
        'japan' => [
            'mode' =>           'Japanese NDB List Country Codes',
            'map' =>            'japan_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'as',
            'shortName' =>      'Japan',
        ],
        'na' => [
            'mode' =>           'North American NDB List Country Codes',
            'stateBtn' =>       'States',
            'stateFilter' =>    'can,usa',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'na',
            'map' =>            'na_map.gif',
            'shortName' =>      'North America',
        ],
        'pacific' => [
            'mode' =>           'Pacific Beacons Map',
            'map' =>            'pacific_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'oc',
            'text' =>
                '(Originally produced for <a href="/dx/ndb/log/steve/?mode=station_list&yyyymm=200307">'
                .'Steve Ratzlaff\'s Pacific Report</a>)',
            'shortName' =>      'Pacific',
        ],
        'polynesia' => [
            'mode' =>           'French Polynesian Beacons Map',
            'map' =>            'map_french_polynesia.gif',
            'shortName' =>      'French Polynesia',
        ],
        'sa' => [
            'mode' =>           'South American NDB List Country Codes',
            'map' =>            'sa_map.gif',
            'countryBtn' =>     'Countries',
            'countryFilter' =>  'sa',
            'shortName' =>      'South America',
        ]
    ];

    const SYSTEM_MAPS = [
        'reu' =>    [
            'maps' =>   ['eu', 'af', 'as' ],
            'title' =>  'Maps for European Listeners'
        ],
        'rna' =>    [
            'maps' =>   [ 'na', 'alaska', 'sa', 'pacific', 'japan', 'polynesia' ],
            'title' =>  'Maps for North American Listeners'
        ],
        'rww' =>    [
            'maps' =>   [ 'af', 'as', 'au', 'eu', 'na', 'sa' ],
            'title' =>  'Maps for All Listeners'
        ],
    ];

// TODO: Add NA Map coords for ATG, ATN and LCA - we have listeners here
// TODO: Fix EU Map code   for SER - should be SRB

    const MAP_COLORS = [
        'eu' => [
            '#e8ffe8' => 'ENG,POR,DEU,ALB,HNG,TUR,LVA,ISL',
            '#e8e8ff' => 'IRL,ESP,SUI,SVK,SMR,NOR',
            '#ffffc0' => 'WLS,GSY,AND,ITA,CZE,SRB,MDA,RUS',
            '#ffc8c8' => 'IOM,FRA,DNK,SVN,CVA,BUL,LTU',
            '#ffd8ff' => 'SHE,JSY,BEL,COR,AUT,BIH,KAL,GRC,UKR,SWE',
            '#ffe098' => 'SCT,LUX,SAR,HRV,MKD,BLR,GEO,FIN',
            '#d0f8ff' => 'GSY,ORK,NIR,HOL,BAL,SCY,LIE,POL,MNE,ROU,EST,FRO'
        ],
        'na' => [
            '#e8ffe8' => 'CA,WA,TX,NM,VA,NE,NC,SC,MN,IL,FL,AZ,MO,OR,MI,CO,IN,HI,AK,AL,MS,PA,NY,TN,MD,WV,ID,NV,UT,MT,WY,ND,SD,KS,OK,AR,LA,GA,IA,KY,OH,DC,DE,NJ,RI,CT,MA,VT,NH,ME,WI,PTR',
            '#e8e8ff' => 'YT,BC,NT,AB,SK,NU,MB,ON,QC,NL,NB,PE,NS',
            '#ffffc0' => 'GRL,MEX,JMC,SVD,PNR',
            '#ffc8c8' => 'BAH,GTM,DOM',
            '#ffd8ff' => 'BLZ,CTR,BER,HTI,VIR',
            '#ffd898' => 'CUB,NCG,VRG',
            '#d0ffff' => 'HND,PTR,CYM'
        ]
    ];

    const MAP_FLOOD = [
        'eu' => [
            'AND' => [[163, 560]],
            'ALB' => [[360, 575]],
            'AUT' => [[300, 490]],
            'AZR' => [], // Appears off the map
            'BAL' => [[165, 606],[178, 597],[189, 595]],
            'BEL' => [[197, 441]],
            'BIH' => [[335, 540]],
            'BLR' => [[440, 390]],
            'BUL' => [[410, 560]],
            'COR' => [[240, 565]],
            'CVA' => [[289, 574]],
            'CZE' => [[310, 450]],
            'DNK' => [[248, 344],[257, 364],[261, 370],[271, 360],[268, 373],[298, 372]],
            'DEU' => [[240, 434]],
            'ENG' => [[133, 401],[132, 440]],
            'ESP' => [[107, 588]],
            'EST' => [[415, 295],[386, 296],[384, 306]],
            'FIN' => [[420, 210],[360, 267],[367, 272],[369, 279]],
            'FRA' => [[170, 515]],
            'FRO' => [[67, 206],[70, 202]],
            'GSY' => [[118, 459]],
            'GIB' => [[90, 642]],
            'GEO' => [[605, 565]],
            'GRC' => [[375, 600],[400, 613],[410, 656],[425, 604],[410, 624],[422, 614],[443, 643]],
            'HNG' => [[350, 500]],
            'HOL' => [[205, 420]],
            'HRV' => [[320, 515]],
            'IOM' => [[94, 382]],
            'IRL' => [[55, 400]],
            'ISL' => [[39, 118]],
            'ITA' => [[270, 545]],
            'JSY' => [[124, 463]],
            'KAL' => [[375, 375]],
            'LIE' => [[249, 493]],
            'LTU' => [[400, 360]],
            'LUX' => [[210, 455]],
            'LVA' => [[415, 330]],
            'MCO' => [[218, 547]],
            'MDA' => [[450, 490]],
            'MKD' => [[377, 573]],
            'MLT' => [[300, 645]],
            'MNE' => [[350, 555]],
            'NIR' => [[65, 375]],
            'NOR' => [[245, 240]],
            'ORK' => [[109, 305]],
            'POL' => [[355, 425]],
            'POR' => [[66, 594]],
            'ROU' => [[405, 510]],
            'RUS' => [[570, 300]],
            'SAR' => [[242, 593]],
            'SCT' => [[68, 319],[61, 328],[73, 331],[76, 347],[74, 358],[69, 359],[83, 355],[86, 361],[100, 340],[61, 334],[60, 339]],
            'SCY' => [[300, 625]],
            'SRB' => [[370, 540]],
            'SHE' => [[134, 268],[134, 273],[129, 278]],
            'SMR' => [[286, 550]],
            'SUI' => [[230, 495]],
            'SVB' => [], // Appears off the map
            'SVK' => [[350, 470]],
            'SVN' => [[300, 510]],
            'SWE' => [[320, 185],[323, 336],[338, 324]],
            'TUR' => [[434, 576],[519, 606]],
            'UKR' => [[480, 465]],
            'WLS' => [[97, 397],[105, 410]],
            'SEA' => [[600, 40],[100, 100],[448, 585],[500, 550]]
        ],
        'na' => [
            'AB' =>     [[260, 180]],
            'AK' =>     [[125, 100],[96, 144]],
            'ALS' =>    [[125, 100],[96, 144]],
            'AL' =>     [[415, 360]],
            'AR' =>     [[375, 340]],
            'ATG' =>    [],
            'ATN' =>    [],
            'AZ' =>     [[245, 345]],
            'BAH' =>    [[475, 415],[482, 418],[477, 433],[481, 429],[489, 430],[494, 435],[503, 438],[498, 446],[506, 451],[514, 453],[513, 464],[521, 457]],
            'BC' =>     [[200, 190],[190, 220],[163, 189]],
            'BER' =>    [[563, 370]],
            'BLZ' =>    [[402, 499]],
            'CA' =>     [[200, 330]],
            'CO' =>     [[290, 310]],
            'CT' =>     [[500, 285]],
            'CTR' =>    [[435, 565]],
            'CYM' =>    [[460, 475]],
            'CUB' =>    [[473, 457],[443, 460]],
            'DC' =>     [[475, 308]],
            'DE' =>     [[486, 311]],
            'DOM' =>    [[535, 485]],
            'FL' =>     [[450, 400],[452, 433]],
            'GA' =>     [[435, 365]],
            'GRL' =>    [[545, 45]],
            'GTM' =>    [[390, 520]],
            'HND' =>    [[415, 520]],
            'HI' =>     [[36, 29],[46, 32],[55, 37],[65, 47]],
            'HWA' =>    [[36, 29],[46, 32],[55, 37],[65, 47]],
            'HTI' =>    [[520, 480]],
            'IA' =>     [[370, 280]],
            'ID' =>     [[240, 265]],
            'IN' =>     [[415, 300]],
            'IL' =>     [[400, 300]],
            'JMC' =>    [[485, 490]],
            'KS' =>     [[340, 315]],
            'KY' =>     [[420, 320]],
            'LA' =>     [[375, 370]],
            'LCA' =>    [],
            'MA' =>     [[505, 280]],
            'MB' =>     [[350, 180]],
            'MD' =>     [[477, 304]],
            'ME' =>     [[515, 255]],
            'MEX' =>    [[320, 450]],
            'MI' =>     [[405, 250],[425, 275]],
            'MN' =>     [[365, 250]],
            'MO' =>     [[375, 315]],
            'MS' =>     [[395, 360]],
            'MT' =>     [[275, 240]],
            'NB' =>     [[535, 245]],
            'NC' =>     [[465, 335]],
            'NCG' =>    [[425, 540]],
            'ND' =>     [[330, 240]],
            'NE' =>     [[330, 290]],
            'NH' =>     [[505, 270]],
            'NJ' =>     [[490, 300]],
            'NL' =>     [[545, 195],[590, 230],[608, 240]],
            'NM' =>     [[285, 345]],
            'NS' =>     [[555, 255]],
            'NT' =>     [[250, 120],[280, 55],[305, 65],[320, 40],[310, 35],[330, 32]],
            'NU' =>     [[350, 115],[415, 112],[325, 75],[380, 55],[330, 45],[360, 60],[425, 125],[440, 130],[347, 45],
                [450, 90],[410, 45],[417, 23],[377, 28],[378, 37],[362, 26],[364, 41],[377, 45],[445, 168],
                [448, 164],[446, 174],[436, 82],[447, 120],[510, 132],[349, 33],[352, 40],[371, 20]
            ],
            'NV' =>     [[220, 305]],
            'NY' =>     [[480, 275],[502, 293]],
            'OH' =>     [[440, 300]],
            'OK' =>     [[340, 340]],
            'ON' =>     [[420, 215]],
            'OR' =>     [[210, 270]],
            'PA' =>     [[470, 295]],
            'PE' =>     [[552, 247]],
            'PNR' =>    [[460, 580]],
            'PR' =>     [[565, 490]],
            'PTR' =>    [[565, 490]],
            'QC' =>     [[480, 200],[547, 222]],
            'RI' =>     [[508, 285]],
            'SC' =>     [[455, 350]],
            'SD' =>     [[330, 265]],
            'SK' =>     [[310, 180]],
            'SVD' =>    [[400, 533]],
            'TN' =>     [[415, 335]],
            'TX' =>     [[330, 370]],
            'UT' =>     [[254, 306]],
            'VA' =>     [[465, 320],[486, 319]],
            'VIR' =>    [[573, 495]],
            'VRG' =>    [[577, 490]],
            'VT' =>     [[500, 265]],
            'WA' =>     [[215, 238]],
            'WI' =>     [[395, 260]],
            'WV' =>     [[450, 310]],
            'WY' =>     [[280, 270]],
            'YT' =>     [[190, 120]],
            'SEA' =>    [[10, 10],[40, 50],[412, 240],[412, 263],[437, 255],[447, 282],[469, 269]]
        ]
    ];

    public function __construct(ParameterBagInterface $params)
    {
        $this->projectDir = $params->get('kernel.project_dir');
        $this->imageDir = $this->projectDir . '/public/image/';
    }

    public static function get($key)
    {
        return static::MAPS[$key];
    }

    public static function getAllForSystem($system)
    {
        $out = [
            'maps' =>   [],
            'title' =>  static::SYSTEM_MAPS[$system]['title']
        ];

        foreach (static::SYSTEM_MAPS[$system]['maps'] as $zone) {
            $out['maps'][$zone] = static::MAPS[$zone];
        }

        return $out;
    }

    public function drawMapImage(
        $region, $mode, $basedIn = false, $reportersIn = false, $reportersData = false, $heardIn = false, $text = false
    ) {
        switch ($region) {
            case 'eu':
                $width = 688;
                $height = 665;
                $legend_x = 8;
                $legend_y = 8;
                break;
            case 'na':
                $width = 653;
                $height = 620;
                $legend_x = 8;
                $legend_y = 538;
                break;
            default:
                return;
                break;
        }

        $this->image = imageCreate($width, $height);
        $this->ImageColorAllocateAll();
        $this->ImageCopyMerge($region . '_map_outline.gif', $width, $height, 100);
        $this->ImageMarkersInit();
        switch ($mode) {
            case 'countries':
                $this->ImageDrawCountries($region);
                break;
            case 'signal':
                if ($reportersIn) {
                    $this->ImageDrawCountryList($region, $reportersIn, $this->colors['no']);
                }
                if ($heardIn) {
                    $this->ImageDrawCountry($region, 'SEA', $this->colors['sea']);
                    $this->ImageDrawCountryList($region, $heardIn, $this->colors['yes']);
                }
                if ($basedIn) {
                    $this->ImageDrawCountry($region, $basedIn, $this->colors['based']);
                }
                if ($text) {
                    $this->ImageDrawLegend($legend_x, $legend_y, $text);
                }
                if ($reportersData) {
                    $this->ImageDrawPointList($reportersData);
                }
                break;
        }
        $this->ImageCopyMerge($region . '_map_codes.gif', 653, 620, 30);

        ImageColorTransparent($this->image, $this->colors['bg']);
        ImageGif($this->image);
        $this->ImageDestroy();
    }

    private function ImageColorAllocate($rgb)
    {
        $r =    hexdec(substr($rgb, 1,2));
        $g =    hexdec(substr($rgb, 3,2));
        $b =    hexdec(substr($rgb, 5,2));
        return ImageColorAllocate($this->image, $r, $g, $b);
    }

    private function ImageColorAllocateAll()
    {
        $this->colors = [
            'bg' =>         $this->ImageColorAllocate('#123456'),
            'black' =>      $this->ImageColorAllocate('#000000'),
            'white' =>      $this->ImageColorAllocate('#ffffff'),
            'darkgray' =>   $this->ImageColorAllocate('#808080'),
            'no' =>         $this->ImageColorAllocate('#ffd2d2'),
            'yes' =>        $this->ImageColorAllocate('#e6ffe6'),
            'based' =>      $this->ImageColorAllocate('#96dcff'),
            'sea' =>        $this->ImageColorAllocate('#f0f0ff')
        ];
    }

    private function ImageCopyMerge($file, $width, $height, $alpha)
    {
        $tmp = imageCreateFromGif($this->imageDir . $file);
        ImageCopyMerge($this->image, $tmp, 0, 0, 0, 0, $width, $height, $alpha);
        ImageDestroy($tmp);
    }

    private function ImageDrawCountry($region, $place, $color)
    {
        if (!$place) {
            return;
        }
        if (!isset(static::MAP_FLOOD[$region][strtoupper($place)])) {
//            print $country. ' ';
            return;
        }
        foreach (static::MAP_FLOOD[$region][strtoupper($place)] as $point) {
            ImageFill($this->image, $point[0], $point[1], $color);
        }
    }

    private function ImageDrawCountries($region)
    {
        foreach (static::MAP_COLORS[$region] as $color => $places) {
            $place_list = explode(',', $places);
            $id = $this->ImageColorAllocate($color);
            foreach ($place_list as $place) {
                foreach (static::MAP_FLOOD[$region][$place] as $point) {
                    ImageFill($this->image, $point[0], $point[1], $id);
                }
            }
        }
    }

    private function ImageDrawCountryList($region, $places, $color)
    {
        foreach ($places as $place) {
            static::ImageDrawCountry($region, $place, $color);
        }
    }

    private function ImageDrawLegend($x, $y, $text)
    {
        $width = 165;
        $height = 72;

        $im =       $this->image;
        $col =      $this->colors;
        $images =   $this->images;

        $this->ImageDrawRectangle($x+2, $y+2, $x+$width+2, $y+$height+2, 3, $col['darkgray'], $col['darkgray']);
        $this->ImageDrawRectangle($x, $y, $x+$width, $y+$height, 3, $col['black'], $col['white']);
        ImageString($im, 4, $x+($width/2) - 25, $y+3, $text['title'], $col['black']);

        $this->ImageDrawRectangle($x+7, $y+21, $x+20, $y+29, 0, $col['darkgray'], $col['based']);
        ImageString($im, 2, $x+26, $y+17, $text['tx'], $col['black']);

        $this->ImageDrawRectangle($x+7, $y+31, $x+20, $y+39, 0, $col['darkgray'], $col['yes']);
        ImageString($im, 2, $x+26, $y+27, $text['yes'], $col['black']);

        $this->ImageDrawRectangle($x+7, $y+41, $x+20, $y+49, 0, $col['darkgray'], $col['no']);
        ImageString($im, 2, $x+26, $y+37, $text['no'], $col['black']);

        ImageCopyMerge($im, $images['yes_pri'], $x+6, $y+51, 0, 0, 9, 9, 100);
        ImageCopyMerge($im, $images['no_pri'], $x+13, $y+51, 0, 0, 9, 9, 100);
        ImageString($im, 2, $x+26, $y+47, $text['pri'], $col['black']);

        ImageCopyMerge($im, $images['yes_sec'], $x+6, $y+61, 0, 0, 9, 9, 100);
        ImageCopyMerge($im, $images['no_sec'], $x+13, $y+61, 0, 0, 9, 9, 100);
        ImageString($im, 2, $x+26, $y+57, $text['sec'], $col['black']);
    }

    private function ImageDrawPointList($items)
    {
        foreach($items as $i) {
            $idx = ($i['heard'] ? 'yes' : 'no') . '_' . ($i['primaryQth'] ? 'pri' : 'sec');
            ImageCopyMerge($this->image, $this->images[$idx], $i['mapX']-4, $i['mapY']-4, 0, 0, 9, 9, 100);
        }
    }

    private function ImageDrawRectangle($x1, $y1, $x2, $y2, $radius, $linecolor, $fillcolor = false)
    {
        $im = $this->image;

        if (!$radius) {
            ImageFilledRectangle($im, $x1, $y1, $x2, $y2, $fillcolor);
            ImageRectangle($im, $x1, $y1, $x2, $y2, $linecolor);
            return;
        }

        if ($fillcolor) {
            imagefilledarc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $fillcolor, IMG_ARC_PIE);
            imagefilledarc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $fillcolor, IMG_ARC_PIE);
            imagefilledrectangle($im, $x1+$radius, $y1, $x2-$radius, $y2, $fillcolor);
            imagefilledrectangle($im, $x1, $y1+$radius, $x2, $y2-$radius, $fillcolor);
        }
        imagearc($im, $x1+$radius, $y1+$radius, $radius*2, $radius*2, 180, 270, $linecolor);
        imagearc($im, $x2-$radius, $y1+$radius, $radius*2, $radius*2, 270, 0, $linecolor);
        imagearc($im, $x1+$radius, $y2-$radius, $radius*2, $radius*2, 90, 180, $linecolor);
        imagearc($im, $x2-$radius, $y2-$radius, $radius*2, $radius*2, 0, 90, $linecolor);
        imageline($im, $x1+$radius, $y1, $x2-$radius, $y1, $linecolor);
        imageline($im, $x1+$radius, $y2, $x2-$radius, $y2, $linecolor);
        imageline($im, $x1, $y1+$radius, $x1, $y2-$radius, $linecolor);
        imageline($im, $x2, $y1+$radius, $x2, $y2-$radius, $linecolor);
    }

    private function ImageMarkersInit()
    {
        $this->images = [
            'yes_pri' =>    imageCreateFromGif($this->imageDir . 'map_point1.gif'),
            'yes_sec' =>    imageCreateFromGif($this->imageDir . 'map_point2.gif'),
            'no_pri' =>     imageCreateFromGif($this->imageDir . 'map_point3.gif'),
            'no_sec' =>     imageCreateFromGif($this->imageDir . 'map_point4.gif')
        ];
    }

    private function ImageDestroy()
    {
        ImageDestroy($this->image);
        ImageDestroy($this->images['yes_pri']);
        ImageDestroy($this->images['yes_sec']);
        ImageDestroy($this->images['no_pri']);
        ImageDestroy($this->images['no_sec']);
    }
}
