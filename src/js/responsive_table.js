var responsive_table = {
    init: function(source, destination) {
        this.source = source;
        this.destination = destination;
        this.drawResponsive();
    },
    drawResponsive: function() {
        var classes, fields, html, i, rows, titles;
        classes = [];
        html = '';
        rows = [];
        fields = [];
        titles = [];
        this.source.find('thead tr th').each(function(){
            var header = $(this);
            fields.push(header.html().trim().replace(/<br>/ig, ' '));
            classes.push(header.prop('title').trim());
            titles.push(header.prop('title').trim());
        });
        this.source.find('tbody tr').each(function(){
            var body = $(this);
            var row = {
                class : body.prop('class'),
                title : body.prop('title')
            };
            i = 0;
            body.find('td').each(function(){
                row[fields[i++]] = $(this).html().trim();
            });
            rows.push(row);
        });
        for (i in rows) {
            html += '<table class="responsive"><tbody>\n';
            for (j in fields) {
                html +=
                    '<tr title="' + titles[i] + '">' +
                    '<th>' + fields[j] + '</th>' +
                    '<td>' + rows[i][fields[j]] + '</td>' +
                    '</tr>\n';
            }
            html += '</tbody></table>\n\n';
        }
        this.destination.append(html);
    }
};