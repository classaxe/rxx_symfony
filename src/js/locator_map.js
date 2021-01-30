// Used here: http://rxx.classaxe.com/en/rna/listeners/323/locatormap
var LocatorMap = {
    init : function(xpos, ypos) {
        var rx_map = $('#rx_map');
        if (!rx_map.height()) {
            return window.setTimeout(function(){ LocatorMap.init(xpos, ypos); }, 100);
        }
        rx_map.on('click', function (e) {
            var x = parseInt(e.pageX - $(this).offset().left);
            var y = parseInt(e.pageY - $(this).offset().top);
            LocatorMap.setPos(x, y);
            $('#form_mapX').val(x);
            $('#form_mapY').val(y);
        });
        $('#form_mapX').change(function() {
            xpos = parseInt($('#form_mapX').val());
            ypos = parseInt($('#form_mapY').val());
            LocatorMap.setPos(xpos, ypos);
        });
        $('#form_mapY').change(function() {
            xpos = parseInt($('#form_mapX').val());
            ypos = parseInt($('#form_mapY').val());
            LocatorMap.setPos(xpos, ypos);
        });
        $('#x_sub').click(function() {
            var form_mapX = $('#form_mapX');
            var val = parseInt(form_mapX.val());
            if (val > 0) {
                form_mapX
                    .val(val - 1)
                    .trigger('change');
            }
        });
        $('#x_add').click(function() {
            var form_mapX = $('#form_mapX');
            var val = parseInt(form_mapX.val());
            form_mapX
                .val(val + 1)
                .trigger('change');
        });
        $('#y_sub').click(function() {
            var form_mapY = $('#form_mapY');
            var val = parseInt(form_mapY.val());
            if (val > 0) {
                form_mapY
                    .val(val - 1)
                    .trigger('change');
            }
        });
        $('#y_add').click(function() {
            var form_mapY = $('#form_mapY');
            var val = parseInt(form_mapY.val());
            form_mapY
                .val(val + 1)
                .trigger('change');
        });
        $('#form_reset').click(function(e) {
            e.preventDefault();
            form = e.toElement.form;
            form.reset();
            xpos = $('#form_mapX').val();
            ypos = $('#form_mapY').val();
            LocatorMap.setPos(xpos, ypos);
        });
        LocatorMap.setPos(xpos, ypos);
        $('#form').show();

    },
    setPos : function(xpos, ypos) {
        if (xpos === 0 && ypos === 0) {
            return;
        }
        $('#cursor').css({
            left : (xpos - 10) + 'px',
            top : (ypos - 10) + 'px',
            display: 'block'
        });
    }
};