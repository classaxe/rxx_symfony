var gridColor = "#808080";
var gridOpacity = 0.5;
var highlight;
var layers = {grid: []};
var map;
var markers = [];
var all_sections = [];

var popWinSpecs = {
    'countries_*' :                 'width=860,height=630,resizable=1',
    'countries_af' :                'width=640,height=630,resizable=1',
    'countries_as' :                'width=780,height=590,resizable=1',
    'countries_eu' :                'width=680,height=590,resizable=1',
    'countries_na' :                'width=640,height=220,resizable=1',
    'countries_oc' :                'width=680,height=500,resizable=1',
    'countries_sa' :                'width=320,height=600,resizable=1',
    'listeners_[id]' :              'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_logs' :         'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signals' :      'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_map' :          'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_locatormap' :   'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_signalsmap' :   'width=880,height=760,status=1,scrollbars=1,resizable=1',
    'listeners_[id]_ndbweblog' :    'status=1,scrollbars=1,resizable=1',
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
    'signals_[id]' :                'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_logs' :           'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_listeners' :      'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_map' :            'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'signals_[id]_weather' :        'width=1040,height=800,status=1,scrollbars=1,resizable=1',
    'states_*' :                    'width=720,height=760,resizable=1',
    'states_aus' :                  'width=720,height=240,resizable=1',
    'states_can_usa' :              'width=680,height=690,resizable=1',
};