var RT = {
    classes: [],
    fields: [],
    preamble: '',
    rows: [],
    titles: [],
    init: function (source, destination) {
        this.source = source;
        this.destination = destination;
        this.readSource();
        this.drawMedium();
        this.drawNarrow();
    },
    drawMedium: function() {
        var colspan = 0, i;
        for (i in RT.fields) {
            if (RT.fields[i].rowspan2) {
                RT.preamble += ('th' === RT.fields[i].type ? '<th></th>' : '<td></td>');
            } else {
                colspan ++;
            }
        }
        i = 0;
        this.source.find('tbody tr').each(function () {
            var ele = $(this), html;

            html = '<table>';
            for (j in RT.fields) {
                if (!RT.rows[i][RT.fields[j].idx].l2) {
                    continue;
                }
                if (!RT.rows[i][RT.fields[j].idx].html) {
                    continue;
                }
                html += '<tr><th>' + RT.fields[j].html + ':</th><td>' + RT.rows[i][RT.fields[j].idx].html + '</td></tr>';
            }
            html += '</table>';

            ele.after(
                '<tr class="' + ele.prop('class') + ' l2_alt">' +
                RT.preamble + '<td colspan="' + colspan + '">' + html + '</td>' +
                '</tr>'
            );
            i++;
        })
    },
    drawNarrow: function() {
        var html, i, j;
        html = '';
        for (i in this.rows) {
            html += '<table class="responsive"><tbody>\n';
            for (j in this.fields) {
                if ('' === this.rows[i][this.fields[j].idx].html) {
                    continue;
                }
                html +=
                    '<tr title="' + this.titles[i] + '">' +
                    '<th>' + this.fields[j].html + '</th>' +
                    '<td>' + this.rows[i][this.fields[j].idx].html + '</td>' +
                    '</tr>\n';
            }
            html += '</tbody></table>\n\n';
        }
        this.destination.append(html);
    },
    readSource: function () {
        var idx = 0;
        this.source.find('thead tr th').each(function () {
            var header = $(this);
            RT.fields.push({
                idx: idx++,
                html: header.html().trim().replace(/<br>/ig, ' '),
                l2: header.hasClass('l2'),
                rowspan2: header.hasClass('rowspan2'),
                type: (header.hasClass('th') ? 'th' : 'td')
            });
            RT.classes.push(header.prop('title').trim());
            RT.titles.push(header.prop('title').trim());
        });
        this.source.find('tbody tr').each(function () {
            var ele = $(this);
            var row = {
//                class: ele.prop('class'),
                title: ele.prop('title')
            };
            var i = 0;
            ele.find('th,td').each(function () {
                var ele = $(this);
                row[RT.fields[i++].idx] = {
                    'l2' : ele.hasClass('l2'),
                    'html' : ele.html().trim()
                };
            });
            RT.rows.push(row);
        });
    }
};