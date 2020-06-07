var DGPS = {
    init: function() {
        $('a[data-dgps]').on('click', function() {
            $('#dgps_ref').val($(this).data('dgps'));
            $('#dgps_go').trigger('click');
            return false;
        });
        $('#dgps_ref').on('focus', function() {
            $(this).select();
        });
        $('#dgps_lookup').on('submit', function() {
            $('#dgps_details').val(DGPS.lookup($('#dgps_ref').val()));
            return false;
        });
        $('#dgps_close').on('click', function(){
            window.close();
        })
    },
    a: function(id1, id2, ref, khz, bps, qth, sta, cnt, act) {
        if (typeof this.entries[id1] == 'undefined') {
            this.entries[id1] =		[];
        }
        this.entries[id1].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);
        if (typeof this.entries[id2] == 'undefined') {
            this.entries[id2] =		[];
        }
        this.entries[id2].push([ ref, khz, bps, qth, sta, cnt, id1, id2, act ]);

    },
    entries: [],
    lookup: function(id) {
        var out = [];
        if (id === '') {
            return '';
        }
        if (typeof this.entries[parseFloat(id)] === 'undefined') {
            return msg.tools.dgps.nomatch;
        }
        id = parseFloat(id);
        for (var i=0; i < this.entries[id].length; i++) {
            var a =	this.entries[id][i];
            out.push(
                '  Station ' + a[0] + (a[8] === 0 ? ' ' + msg.tools.dgps.inactive : '') + "\n" +
                '  ' + a[1] + 'KHz ' + a[2] + 'bps' + "\n" +
                '  ' + a[3] + ' ' + a[4] + ' ' + a[5] + "\n" +
                '  Reference ID(s): ' + a[6] + (a[6] !== a[7] ? ', ' + a[7] : '')
            );
        }
        if (i>1) {
            return msg.tools.dgps.multiple + ' (' + i + "):\n"+out.join("\n\n");
        }
        return out.join("");
    }
}
