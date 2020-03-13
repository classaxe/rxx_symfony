var SLMap = {
    init : function() {
        var html = '', i, imgmap = '', l;
        for (i in listeners) {
            l = listeners[i];
            html +=
                '<tr id="listener_' + l.id + '" data-map="' + l.x + '|' + l.y + '|' + l.id +'"' + (l.dt ? ' title="' + msg.daytime + '"' : '') +'>\n' +
                '<td>\n' +
                '<img src="' + base_image + '/map_point' + (l.pri ? 1 : 2) +'.gif" alt="' + (l.pri ? msg.qth_pri : msg.qth_sec) + '" />\n' +
                '<a href="' + base_url + 'listeners/' + l.id + '" class="' + (l.pri ? 'pri' : 'sec') + '">' + l.name + '</a>\n' +
                '</td>\n' +
                '<td>' + l.sp + '</td>\n' +
                '<td>' + l.itu + '</td>\n' +
                '<td>' + (l.dt ? '<b>' + l.km + '</b>' : l.km ) + '</td>\n' +
                '<td>' + (l.dt ? '<b>' + l.mi + '</b>' : l.mi ) + '</td>\n' +
                '</tr>\n';
            imgmap +=
                '<area alt="' + l.name + '" title="' + l.name + '" shape="circle" href="' + base_url + 'listeners/' + l.id + '" coords="' + l.x + ',' + l.y + ',4" data-map="' + l.id + '" />\n';
        }
        $('.results tbody').html(html);
        $('#imgmap').html(imgmap);
    }
};
