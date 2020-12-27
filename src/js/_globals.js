var gridColor = "#808080";
var gridOpacity = 0.5;
var highlight;
var layers = {grid: []};
var map;
var markers = [];
var all_sections = [];
var award = {};
var cart = [];

var popWinSpecs = {
    'admin_users_[id]' :            'width=420,height=320,status=1,scrollbars=1,resizable=1',
    'admin_users_new' :             'width=420,height=320,status=1,scrollbars=1,resizable=1',
    'countries_*' :                 'width=860,height=630,resizable=1',
    'countries_af' :                'width=640,height=630,resizable=1',
    'countries_as' :                'width=780,height=590,resizable=1',
    'countries_eu' :                'width=680,height=590,resizable=1',
    'countries_na' :                'width=640,height=220,resizable=1',
    'countries_oc' :                'width=680,height=500,resizable=1',
    'countries_sa' :                'width=320,height=600,resizable=1',
    'listeners_[id]' :              'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logs' :         'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logsessions' :  'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_upload' :       'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signals' :      'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_map' :          'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_locatormap' :   'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    '[id]_signals_map' :            'width=1000,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_ndbweblog' :    'status=1,scrollbars=1,resizable=1',
    'logs_[id]' :                   'width=640,height=500,status=1,scrollbars=1,resizable=1',
    'maps_af' :                     'width=646,height=652,resizable=1',
    'maps_alaska' :                 'width=600,height=620,resizable=1',
    'maps_as' :                     'width=856,height=645,resizable=1',
    'maps_au' :                     'width=511,height=545,resizable=1',
    'maps_eu' :                     'width=704,height=760,resizable=1',
    'maps_japan' :                  'width=517,height=740,resizable=1',
    'maps_na' :                     'width=669,height=720,resizable=1',
    'maps_pacific' :                'width=600,height=750,resizable=1',
    'maps_polynesia' :              'width=500,height=525,resizable=1',
    'maps_sa' :                     'width=490,height=745,resizable=1',
    'signals_new' :                 'width=720,height=320,status=1,scrollbars=1,resizable=1',
    'signals_[id]' :                'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_logs' :           'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_listeners' :      'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_map' :            'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_weather' :        'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'states_*' :                    'width=720,height=760,resizable=1',
    'states_aus' :                  'width=720,height=240,resizable=1',
    'states_can_usa' :              'width=680,height=690,resizable=1',
    'tools_coordinates' :           'width=900,height=195,resizable=1',
    'tools_dgps' :                  'width=720,height=345,resizable=1',
    'tools_navtex' :                'width=420,height=580,resizable=1',
    'tools_negativeKeyer' :         'width=800,height=560,resizable=1',
    'tools_references' :            'width=520,height=130,resizable=1',
    'tools_sunrise' :               'width=520,height=385,resizable=1',
    'weather_aurora_n' :            'width=520,height=580,resizable=1',
    'weather_aurora_s' :            'width=520,height=580,resizable=1',
    'weather_lightning' :           'width=620,height=620,resizable=1',
};