$(document).ready(function () {
  return;
    $(document).on('click', 'a[data-popup]', function (e) {
        var box = $(this);
        if (typeof box.attr('data-confirm') == 'undefined') {
            e.preventDefault();
            box.data('popup', (new Popup({
                url: box.attr('href'),
            }).init(true)));
            return false;
        }
    });
});



function status_contrato_estado(cid) {
  let fft = {
    58: 'PRECONTRATO',
    59: 'CONTRATO',
    60: 'INICIO DE INSTALACIÃ“N',
    61: 'FIN DE INSTALACIÃ“N',
    62: 'INICIO DE DESARROLLO',
    63: 'FIN DE DESARROLLO',
    64: 'INICIO DE SERVICIO',
    65: 'FIN DE SERVICIO',
    66: 'INICIO DE GARANTÃA',
    67: 'FIN DE GARANTÃA',
    68: 'CANCELADO',
    69: 'CONCLUIDO',
  };
  if(typeof fft[cid] !== 'undefined') {
    return fft[cid];
  } else {
    return '--';
  }
}
function status_moneda(mid) {
  if(mid == 1) {
    return 'SOLES';
  } else if (mid == 2) {
    return 'DOLARES';
  } else if (mid == 3) {
    return 'EUROS';
  }
}
function status_monto(mid, nn) {
  var pre = '';
  var pos = '';
  if(mid == 1) {
    pre = 'S/. ';
  } else if(mid == 2) {
    pos = ' USD';
  } else if(mid == 3) {
    pos = ' EUR';
  }
  nn = parseFloat(nn);
  return pre + nn.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,') + pos;
}
function wdir(file) {
  console.log('wdir', this);
  if(typeof window.baseUserDir == 'undefined') {
    return alert('no dir');
  }
  var comm = decodeURI('odir:' + window.baseUserDir + '/' + file);
  comm = comm.replace(/\//g, "\\")
  console.log('execute', comm);
  window.location.href = comm;
}
function status_wdir(txt, dir) {
  return "<a href=\"javascript:wdir('" + encodeURI(dir) + "');\">" + txt + '</a>';
}
function status_select_estado(eid) {
  if(eid == 1) {
    return '<div style="color:#c1c1c1;">PENDIENTE</div>';
  } else if(eid == 2) {
    return '<div style="color:#ff7600;">EN PROCESO</div>';
  } else if(eid == 3) {
    return '<div style="color:#6ed130;">FINALIZADO</div>';
  } else {
    return '';
  }
}
function status_date_vencimiento(dd, estado) {
  if(dd == '' || dd == null) {
    return '';
  }
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const pp = new Date(dd);
  pp.setHours(0, 0, 0, 0);

  var diff = new Date(+today) - new Date(+pp);
  diff = Math.round(diff/8.64e7);

  if(pp <= today) {
    if(estado) {
      return '<span><-' + pp.toLocaleString().split(',')[0] + '</span>';
    } else {
      return '<span style="color:red"><-' + pp.toLocaleString().split(',')[0] + '</span>';
    }
  } else if(diff > 7) {
    return '<span>->' + pp.toLocaleString().split(',')[0] + '</span>';
  } else if(diff <= 7) {
    return '<span style="color: orange">->' + pp.toLocaleString().split(',')[0] + '</span>';
  } else {
    return '<span>' + pp.toLocaleString().split(',')[0] + '</span>';
  }
}
function status_date(dd, estado) {
  if(dd == '' || dd == null) {
    return '';
  }
  const today = new Date();
  today.setHours(0, 0, 0, 0);
  const pp = new Date(dd);
  if(pp < today) {
    return '<span><-' + pp.toLocaleString().split(',')[0] + '</span>';
  } else if(pp > today) {
    return '<span>->' + pp.toLocaleString().split(',')[0] + '</span>';
  } else {
    return '<span>' + pp.toLocaleString().split(',')[0] + '</span>';
  }
}
function status_badge(tt) {
  tt = JSON.parse(tt);
  return '<span class="' + tt.class + '">' + tt.message + '</span>';
}
