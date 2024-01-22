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
function _hasTag(box, tag) {
  if(typeof box.tags !== 'undefined') {
    return true;
  }
  return false;
}
function _addTag(box, tag) {
  if(typeof box.tags === 'undefined') {
    box.tags = {};
  }
  if(typeof box.tags[tag] == 'undefined') {
    box.tags[tag] = tag;
    return true;
  }
  return false;
}
function toHHMMSS(text) {
    var sec_num = parseInt(text, 10); // don't forget the second param
    var hours   = Math.floor(sec_num / 3600);
    var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
    var seconds = sec_num - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    return hours+':'+minutes+':'+seconds;
}
var requestId = 0;
var timeFetchx = {};
function navigate(href, newTab) {
   var a = document.createElement('a');
   a.href = href;
   if (newTab) {
      a.setAttribute('target', '_blank');
   }
   a.click();
}
function merge(first, second) {
    first = first || second;
    for(var index in second) {
        if(second.hasOwnProperty(index)) {
            first[index] = second[index];
        }
    }
    return first;
}

function Fetchx(params, inmediate) {
  var idToast = null;
    if (
        typeof params.id !== "undefined" &&
        typeof params.delay !== "undefined"
    ) {
        inmediate = inmediate || false;
        if (!inmediate) {
            clearTimeout(timeFetchx[params.id]);
            return (timeFetchx[params.id] = setTimeout(function () {
                Fetchx(params, true);
                timeFetchx[params.id] = null;
            }, params.delay));
        }
    }
    var myId = ++requestId;
    var mostrar = function () {
        if (typeof params.loading !== "undefined") {
            $(params.loading)
                .addClass("request_" + myId)
                .slideDown();
        }
        if(typeof params.title !== 'undefined') {
          idToast = toastr.info('Se está procesando la solicitud', params.title, { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 0 });
        }
    };
    var ocultar = function () {
        if (typeof params.loading !== "undefined") {
            $(params.loading).removeClass("request_" + myId);
            if ($(params.loading).attr("class") == "") {
                $(params.loading).slideUp();
            }
        }
    };

    if (typeof params.beforeSend !== "undefined") {
        var temp_before = params.beforeSend;
        params.beforeSend = function () {
            mostrar();
            temp_before();
        };
    } else {
        params.beforeSend = function () {
            mostrar();
        };
    }

    if (typeof params.success !== "undefined") {
        var temp_success = params.success;
        params.success = function ($x) {
            ocultar();
            if(typeof params.title !== 'undefined') {
              toastr.clear(idToast);
              toastr.success('Se ha realizado con éxito', params.title, { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 2000 });
            }
          temp_success($x);
        };
    } else {
        params.success = function ($x) {
            ocultar();
            if(typeof params.title !== 'undefined') {
              toastr.clear(idToast);
              toastr.success('Se ha realizado con éxito', params.title, { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 2000 });
            }
        };
    }
    var temp_error = params.error;
    params.error = function ($x) {
      typeof temp_error !== 'undefined' && temp_error();
      ocultar();
      if(typeof params.title !== 'undefined') {
        toastr.clear(idToast);
        toastr.error('Ocurrió un problema durante la ejecución', params.title, { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 2000 });
      }
    };
    return $.ajax(params);
}
function FetchxConfirm(params) {
    var n = Swal.mixin({
        buttonsStyling: false,
        customClass: {
            confirmButton: "btn btn-alt-success m-5",
            cancelButton: "btn btn-alt-danger m-5",
            input: "form-control",
        },
    });
    return n.fire({
        title: "¿Está seguro de realizar esta acción?",
        text: "Si realiza la acción, puede que la informacián almacenada no pueda recuperarse.",
        icon: "warning",
        showCancelButton: true,
        customClass: {
            confirmButton: "btn btn-alt-danger m-1",
            cancelButton: "btn btn-alt-secondary m-1",
        },
        confirmButtonText: "Efectuar!",
        cancelButtonText: "Cancelar",
        html: false,
    }).then(function (e) {
        if (e.value) {
            Fetchx(merge(params, {
                success: function (data) {
                    if (data.status) {
                        /*n.fire(
                            "Realizado!",
                            data.message,
                            "success"
                        );*/
                        typeof params.success === 'function' && params.success(data);
                        if (typeof data.refresh !== "undefined" && data.refresh) {
                            setTimeout(function () {
                                location.reload();
                            }, 2000);
                        }
                    } else if (data.required_force) {
                        return n.fire({
                            title: "Advertencia de procedimiento",
                            text: data.warning,
                            icon: "warning",
                            showCancelButton: true,
                            customClass: {
                                confirmButton: "btn btn-alt-danger m-1",
                                cancelButton: "btn btn-alt-secondary m-1",
                            },
                            confirmButtonText: "Forzar!",
                            cancelButtonText: "Cancelar",
                            html: false,
                        }).then(function (e) {
                            if (e.value) {
                                Fetchx(merge(params, {
                                    data: merge(params.data, { is_force: 1 }),
                                    success: function (data) {
                                        if (data.status) {
                                            n.fire(
                                                "Realizado!",
                                                data.message,
                                                "success"
                                            );
                                            typeof params.success === 'function' && params.success(data);
                                            if (typeof data.refresh !== "undefined" && data.refresh) {
                                                setTimeout(function () {
                                                    location.reload();
                                                }, 2000);
                                            }
                                        } else {
                                            n.fire("Denegado", data.warning, "error");
                                        }
                                    },
                                }));
                            } else if ("cancel" === e.dismiss) {
                                n.fire(
                                    "Cancelado",
                                    "El proceso ha sido cancelado! :)",
                                    "error"
                                );
                            } else {
                                n.fire(
                                    "Cancelado",
                                    "El proceso ha sido cancelado.",
                                    "error"
                                );
                            }
                        });
                    } else {
                        n.fire("Denegado", data.warning, "error");
                    }
                },
            }));
        } else if ("cancel" === e.dismiss) {
            n.fire(
                "Cancelado",
                "El proceso ha sido cancelado! :)",
                "error"
            );
        } else {
            n.fire(
                "Cancelado",
                "El proceso ha sido cancelado.",
                "error"
            );
        }
    });
};

function render_autocomplete() {
    $(":input.autocomplete").each(function () {
      if(!_addTag(this, 'autocomplete')) {
        return false;
      }
      var box = $(this);
        var rotulo = box.attr("data-value");
        var url = box.attr("data-ajax");
        var form = box.attr("data-register");
        var name = box.attr("name");
        var boxId = $("<input>")
            .attr("type", "text")
            .attr("name", name)
            .attr("value", box.attr("value"))
            .css({
                position: "fixed",
                opacity: 0,
                width: 0,
                height: 0,
                visibility: "collapse", 
                float: "left",
            });
        boxId.attr("required", box.attr("required"));
        boxId.attr("autocomplete", "no");
        box.attr("autocomplete", "no");
        //box.removeAttr("required");
        box.removeAttr("data-ajax");
        box.removeAttr("name")
            .attr("name", name + "_rotulo")
            .attr("value", box.attr("data-value"));
        box.after(boxId);
        box.removeClass("autocomplete");

        if(typeof box.attr('data-editable') !== 'undefined') {
          boxId.attr('data-editable', box.attr('data-editable'));
          box.removeAttr('data-editable');
        }
        if (typeof form !== "undefined") {
            var btn = $("<button>")
                .attr("type", "button")
                .addClass("btn btn-secondary")
                .text("Nuevo")
                .css({
                  "margin-left": "5px"
                });
              
            box.before($("<div>").addClass("input-float-append").html(btn));
            btn.on("click", function () {
                if ($(".modal[data-url='" + form + "']").length && false) { /* TODO:  Quitamos el cachÃƒÂ¨ */
                    var modal = $(".modal[data-url='" + form + "']");
                } else {
                    var modal = $("<div>")
                        .addClass("modal fade")
                        .attr("data-url", form)
                        .attr("role", "dialog")
                        .attr("aria-labelledby", "modal-fadein")
                        .attr("aria-hidden", "true");
                    modal.html(
                        '<div class="modal-dialog" role="document"><div class="modal-content">Cargando...</div></div>'
                    );
                    Fetchx({
                        url: form,
                        type: "GET",
                        success: function (data) {
                            console.log("Success");
                            modal.find(".modal-content").html(data);
                            modal
                                .find("form")
                                .off("submit")
                                .on("submit", function (e) {
                                    e.preventDefault();
                                    var formulario = modal.find("form");
                                    return Fetchx({
                                        url: formulario.attr("action"),
                                        type: "POST",
                                        data: formulario.serialize(),
                                        success: function (response) {
                                            if (
                                                typeof response.status ===
                                                "undefined"
                                            ) {
                                                return;
                                            }
                                            console.log(
                                                "response",
                                                response.status
                                            );
                                            if (response.status == "success" || response.status === true) {
                                                modificacion_permitida = true;
                                                box.attr(
                                                    "data-value",
                                                    response.data.value
                                                );
                                                box.val(response.data.value);
                                                boxId.val(response.data.id);
                                                boxId.attr(
                                                    "data-value",
                                                    response.data.value
                                                );
                                                console.log('caja', box);
                                                /*if(typeof  data.precio != 'undefined') {
                                                }*/
                                                boxId.change();
                                                modal.modal("hide");
                                                if (typeof boxId.attr("data-autocomplete-finish") !== "undefined") {
                                                    if (
                                                        typeof window[boxId.attr("data-autocomplete-finish")] !==
                                                        "undefined"
                                                    ) {
                                                        window[boxId.attr("data-autocomplete-finish")].call(
                                                            boxId[0],
                                                            response.data
                                                        );
                                                    } else {
                                                        console.log(
                                                            "Función no existe:" +
                                                            boxId.attr("data-autocomplete-finish")
                                                        );
                                                    }
                                                } else {
                                                    console.log('OJO2: No tiene data-autocomplete-finish', boxId);
                                                }
                                            } else if (
                                                response.status == "error"
                                            ) {
                                                var alerta = $("<div>")
                                                    .addClass(
                                                        "alert alert-danger alert-dismissable"
                                                    )
                                                    .hide();
                                                alerta.text(response.message);
                                                modal
                                                    .find(".modal-content")
                                                    .prepend(alerta);
                                                alerta.slideDown();
                                                setTimeout(function () {
                                                    alerta.slideUp(
                                                        500,
                                                        function () {
                                                            $(this).remove();
                                                        }
                                                    );
                                                }, 2000);
                                            }
                                        },
                                    });
                                });
                        },
                        error: function () {
                            modal.remove();
                            alert("No se ha podido conectar con el servidor");
                        },
                        complete: function () {
                            micro_ready();
                            var formulario = modal.find("form");
                            //modal.hidde();
                        },
                    });
                }
                modal.on("hidden.bs.modal", function () {
                    //setTimeout(function() {
                    //modal.remove();
                    //}, 500);
                });
                $("body").append(modal);
                modal.modal("show");
            });
            /*if(typeof $(this).closest('input-group') !== 'undefined') {
                var topa = $("<div>").addClass('input-group');
                $(this).parent().append(topa);
                $(this).appendTo(topa);
            }*/
        }

        var modificacion_permitida = false;
        var modificaron = function () {
            console.log("Modificaron", box[0]);
            boxId.val("");
            box.attr("data-value", "");
            boxId.attr("data-value", "");
            box[0].value = "";
        };
        const conf = new Bloodhound({
            datumTokenizer: (datum) =>
                Bloodhound.tokenizers.whitespace(datum.value),
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url:
                    url + (url.indexOf("?") == -1 ? "?" : "&") + "query=%QUERY",
            },
        });
        conf.initialize();
        box.typeahead(
            {
                minLength: 1,
                highlight: true,
            },
            {
                displayKey: "value",
                source: conf.ttAdapter(),
            }
        );
        var boxAttr = box[0].attributes;
        console.log('ATTRS', boxAttr);
        for (var index in boxAttr) {
            if (
                typeof boxAttr[index].name !== "undefined" &&
                boxAttr[index].name.startsWith("data-")
            ) {
                if (boxAttr[index].name === "data-disabled") {
                    box.attr("disabled", "true");
                } else { 
                    boxId.attr(boxAttr[index].name, boxAttr[index].value);
                }
            }
        }
        box.on("change", function () {
            console.log(
                "change!",
                "Antes",
                box.attr("data-value"),
                "Ahora",
                box.val()
            );
            if (
                !modificacion_permitida &&
                box.attr("data-value") !== box.val()
            ) {
                console.log("change Ok!", this, box);
                setTimeout(modificaron, 100);
            }
            modificacion_permitida = false;
        });
        box.on("typeahead:selected", function (ev, item) {
            console.log("Se escoge el item:", item);
            modificacion_permitida = true;
            if(typeof detalle !== 'undefined' && detalle && typeof item.precio_unidad !== 'undefined') {
              var tr = boxId.closest('tr');
              console.log(tr.children);  
              var index = $("#productos tr").index(tr);
              //  .val( item.precio_unidad );
              console.log(index);
              var precio = document.querySelector(`#productos tr:nth-child(${ index + 1 } )  .precio`)
              var cantidad = document.querySelector(`#productos tr:nth-child(${ index + 1 } )  .cantidad`)
              precio.value = item.precio_unidad;
              cantidad.value = cantidad.value > 0 ? cantidad.value : 1; 
              detalle[index].producto_id = item.id;
              console.log( precio.value );
              var event = new Event('keyup');
              precio.dispatchEvent(event);
              cantidad.dispatchEvent(event);
              detalle[index].monto = item.precio_unidad;
              console.log(detalle);
            }
            console.log("typeahead:selected");
            boxId.attr("value", item.id);
            boxId.val(item.id);
            boxId.attr("data-value", item.value);
            if (typeof item.data !== "undefined") {
                for (var index in item.data) {
                    if (item.data.hasOwnProperty(index)) {
                        if (index === "disabled") {
                            box.attr("disabled", "true");
                        } else {
                            boxId.attr("data-" + index, item.data[index]);
                        }
                    }
                }
            }
            boxId.change();
            if (typeof boxId.attr("data-autocomplete-finish") !== "undefined") {
                if ( typeof window[boxId.attr("data-autocomplete-finish")] !== "undefined") {
                    window[boxId.attr("data-autocomplete-finish")].call(
                        boxId[0],
                        item
                    );
                } else {
                    console.log(
                        "Funcion no existe:" +
                        boxId.attr("data-autocomplete-finish")
                    );
                }
            } else {
                console.log("OJO1: No tiene data-autocomplete-finish", boxId);
            }
        });
    });
}
function render_input_ajax() {
    $(".form-ajax").each(function () {
      if(!_addTag(this, 'form-ajax')) {
        return false;
      }
      var box = $(this);
        box.removeClass("form-ajax");
        var url = box.attr("data-ajax");
        box.on("change", function () {
            Fetchx({
                url: url,
                type: "POST",
                data: {
                    _token: $("[name='_token']").val(),
                    id: box.val(),
                },
                beforeSend: function () { },
                success: function (data) {
                    eval(data);
                },
                error: function (e) {
                    console.log("error", e);
                    alert("Ha ocurrido un problema inesperado.");
                },
            });
        });
    });
}
function render_bootstrap() {
  return;
  $("[data-toggle='dropdown']").each(function() {
    if($(this).attr('data-ignore') === true) {
      return;
    }
    $(this).attr('data-ignore', true);
    $(this).dropdown();
  });
}
function form_select_fill(field, data) {
    console.log("form_select_fill", field, data);
    var options = "";
    for (var x = 0; x < data.length; x++) {
        options +=
            '<option value="' +
            data[x]["id"] +
            '">' +
            data[x]["value"] +
            "</option>";
    }
    $("select[name='" + field + "']")
        .html(options)
        .change();
}
function render_select_default() {
    $("select[data-value]").each(function () {
      if(!_addTag(this, 'data-value')) {
        return false;
      }
      var box = $(this);

      var vvv = $(this).attr("data-value");
      $(this).removeAttr('data-value');
        if (vvv != "") {
            if (typeof $(this).attr("data-ajax") !== "undefined") {
                $(this).val(vvv);
            } else {
                $(this).val(vvv).change();
            }
        }
    });
}
function time_input_js(time, formato) {
    let dt = "00:00:00";
    if (!time) return dt;
    if (typeof time !== "string" && typeof time !== "number") {
        console.warn("Time in time-input is not a string or a number!");
        return dt;
    }
    let t, l;
    let h = "00";
    let m = "00";
    let s = "00";
    let sep = ":";
    t = time.toString().replace(/[^0-9]/gm, "");
    l = t.length;
    if (!parseInt(t)) return dt;
    function handler(type, value) {
        let r;
        let l = type === "h" ? 23 : 59;
        r = value.length === 1 ? "0" + value : parseInt(value) > l ? h : value;
        return r;
    }
    function fh() {
        return handler("h", t.substr(0, 2));
    }
    function fm() {
        return handler("m", t.substr(2, 2));
    }
    function fs() {
        return handler("s", t.substr(4, 2));
    }
    if (l <= 2) {
        h = fh();
    }
    if (l === 3 || l === 4) {
        h = fh();
        m = fm();
    }
    if (l === 5 || l >= 6) {
        h = fh();
        m = fm();
        s = fs();
    }
    return h + sep + m + sep + s;
}
function render_input_time() {
    $("input[data-format]").each(function () {
      if(!_addTag(this, 'format')) {
        return false;
      }
        var box = this;
        var format = $(box).attr("data-format");
        $(box).removeClass("data-format");
        if (format == "time") {
            var timeKey = null;
            $(box).on("keypress", function () {
                if (timeKey !== null) {
                    clearTimeout(timeKey);
                }
                timeKey = setTimeout(function () {
                    box.value = time_input_js(box.value, format);
                    timeKey = null;
                }, 1000);
            });
        }
    });
}
function render_time_left() {
  $("[data-time-left]").each(function() {
    if(!_addTag(this, 'time-left')) {
      return false;
    }
    var box  = this;
    var text = $(box).attr("data-time-left") || '0';
    text     = parseInt(text);
    var restar = function(domi, seconds) {
      if(seconds <= 0) {
        $(domi).text('En breve Instantes...');
      } else {
        setTimeout(function() {
          restar(domi, seconds - 1);
        }, 1000);
        $(domi).text(toHHMMSS(seconds));
      }
    };
    restar(box, text);
  });
}
function render_confirm_input() {
  $("[data-confirm-input]").each(function () {
    if(!_addTag(this, 'confirm-input')) {
      return false;
    }
    var box  = this;
    var text = $(box).attr("data-confirm-input") || 'Â¿Cual fue el motivo?';
    var url  = $(box).attr('href');
    $(this).on("click", function (e) {
      if(typeof $(box).attr('data-skip2') === 'undefined' && typeof $(box).attr('disabled') === 'undefined') {
        e.preventDefault();
        e.stopImmediatePropagation();
            var modal = $("<div>")
                .addClass("modal fade")
                .attr("role", "dialog")
                .attr("aria-labelledby", "modal-fadein")
                .attr("aria-hidden", "true");
            modal.html('<div class="modal-dialog"><div class="modal-content" style="box-shadow: none;background-color: transparent;margin-top: 20%;"><div class="modal-body">'+ 
                  '<p class="text-white text-large fw-light mb-1">' + text + '</p>' +
                  '<div class="input-group input-group-lg mb-1">' +
                  '<input type="text" class="form-control bg-white border-0">' +
                  '<button class="btn btn-primary" type="button" id="subscribe">Realizar</button>' +
                  '</div><div class="text-start text-white opacity-50">Debe indicar el motivo de su solicitud</div></div></div></div>');
            $("body").append(modal);
            modal.modal("show");
            modal.find("button.btn-primary").on("click", function () {
              if($(box).data('fn_dinamic') === 1) {
                modal.modal('hide');
                $(box).attr('data-skip2', true);
                $(box).attr('data-pass-value', modal.find('input.form-control').val());
                $(box).click();
              } else {
                Fetchx({
                  url: url,
                  type: 'POST',
                  headers : {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
                    },
                  data: {
                    value: modal.find('input.form-control').val(),
                  },
                  dataType: 'json',
                  success: function(res) {
                    if(!res.status) {
                      modal.find('.text-start').text(res.message);
                    } else {
                      $(box).slideUp();
                      console.log('MODAL', modal);
                      modal.modal("hide");
                      modal.remove();
                      $('body').removeClass('modal-open');
                      $(".modal-backdrop").remove();
                    }
                  }
                });
              }
            });
            return false;
      } else {
        $(box).removeAttr('data-skip2');
      }
    });
  });
}
function render_link_confirm() {
    $("[data-confirm]").each(function () {
      if(!_addTag(this, 'confirm')) {
        return false;
      }
      $(this).attr('data-confirm-ready', 1);
        var box = this;
        var text = $(box).attr("data-confirm") || "Realizar";
        $(this).on("click", function (e) {
          if(typeof $(box).attr('data-skip') === 'undefined' && typeof $(box).attr('disabled') === 'undefined') {
            e.preventDefault();
            e.stopImmediatePropagation();
            var modal = $("<div>")
                .addClass("modal fade")
                .attr("role", "dialog")
                .attr("aria-labelledby", "modal-fadein")
                .attr("aria-hidden", "true");
            modal.html(
                '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">Confirmación</h5></div><div class="modal-body">Es necesaria su aprobación para poder realizar la acción en el sistema.</div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button><button class="btn btn-primary">' +
                text +
                "</button></div></div></div>"
            );
            $("body").append(modal);
            modal.find("button.btn-primary").on("click", function () {
              modal.modal('hide');
              $(box).attr('data-skip', true);
              $(box).click();
            });
            modal.modal("show");
            return false;
          } else {
            $(box).removeAttr('data-skip');
          }
        });
    });
}
function render_block_dinamic() {
  $("[data-block-dinamic]").each(function () {
    if(!_addTag(this, 'block-dinamic')) {
      return false;
    }
    var box = $(this);
    var boxt = $('<div>').addClass('block_dinamic_time');
    box.append(boxt);
    var boxc = $('<div>').addClass('block_dinamic_content');
    box.append(boxc);
    var url = box.attr('data-block-dinamic');
    var auto = box.attr('data-block-auto') ?? false;
    var refresh = box.attr('data-block-refresh') ?? 0;
    
    var click = function() {
      Fetchx({
//        title: 'Cargando...',
        url: url,
        type: 'POST',
        headers : {
         'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        success: function(res) {
          boxc.html(res);
        },
        complete: function() {
          if(refresh > 1) {
            var ii = 0;
            var cc = null;
            cc = setInterval(function() {
              ii++;
              if(refresh <= ii) {
                click();
                boxt.slideUp();
                ii = 0;
                clearInterval(cc);
              } else {
                boxt.slideDown();
                boxt.text('Actualizando en ' + (refresh - ii) + ' segundos');
              }
            }, 1000);
          }
        }
      });
    };
    if(auto) {
      boxc.html('<div class="block_dinamic_loading">Cargando...</div>');
      click();
    } else {
      boxc.html('<div class="block_dinamic_button">CLICK PARA VER CONTENIDO</div>');
      boxc.on('click', function() {
        box.off('click');
        click();
      });
    }
  });
}
function render_dom_popup() {
  console.log('render_dom_popup');
    $("[data-popup]").each(function () {
      if(!_addTag(this, 'popup')) {
        return false;
      }
        var box = $(this);
        var form = box.attr("data-popup");
        if(form === '1' || form === "true") {
          form = null;
        }
        var title = box.attr('data-title') || 'Proceso';
        $(this).attr("data-url-nn", form);
        box.removeAttr("data-popup");
        box.on("click", function (e) {
          if (typeof box.attr('data-confirm') === 'undefined') {
            e.preventDefault();
            box.data('popup', (new Popup({
                url: form || box.attr('href'),
            }).init(true)));
            return false;
          }
        });
    });
}
function render_confirm_remove() {
    $("[data-confirm-remove]").each(function () {
      if(!_addTag(this, 'confirm-remove')) {
        return false;
      }
      var box = $(this);
        var url = $(this).attr("data-confirm-remove");
        box.removeAttr("data-confirm-remove");

        var n = Swal.mixin({
            position: 'center',
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
        });
        box.on("click", function (e) {
            n.fire({
                title: "¿Esta seguro de eliminar?",
                text:
                    "De realizarse esta accion no podra acceder nuevamente a la informacion.",
                icon: "warning",
                showCancelButton: true,
                customClass: {
                    confirmButton: "btn btn-alt-danger m-1",
                    cancelButton: "btn btn-alt-secondary m-1",
                },
                confirmButtonText: "Si, eliminar!",
                html: false,
            }).then(function (e) {
                if (e.value) {
                    Fetchx({
                      title: 'Eliminación',
                        url: url,
                        type: "DELETE",
                        headers : {
                         'X-CSRF-TOKEN' : $('meta[name="csrf-token"]').attr("content")
                        },
                        success: function (data) {
                            if (data.status) {
                                n.fire(
                                    "Eliminado!",
                                    "El objeto ha sido eliminado de forma correcta.",
                                    "success"
                                );
                            } else {
                                n.fire("Denegado", data.message, "error");
                            }
                            if (typeof data.refresh !== "undefined" && data.refresh) {
                                setTimeout(function () {
                                    location.reload();
                                }, 2000);
                            }
                        },
                    });
                } else if ("cancel" === e.dismiss) {
                    n.fire(
                        "Cancelado",
                        "El proceso ha sido cancelado! :)",
                        "error"
                    );
                } else {
                    n.fire(
                        "Cancelado",
                        "El proceso ha sido cancelado.",
                        "error"
                    );
                }
            });
        });
    });
}
function render_button_dinamic() {
  var n = Swal.mixin({
    buttonsStyling: false,
    customClass: {
      confirmButton: "btn btn-alt-success m-5",
      cancelButton: "btn btn-alt-danger m-5",
      input: "form-control",
    },
  });
  $('[data-button-dinamic]').each(function() {
    if(!_addTag(this, 'button-dinamic')) {
      return false;
    }
    $(this).data('fn_dinamic', 1).removeAttr('data-button-dinamic').on('click', function(e) {
      e.preventDefault();
      if(typeof $(this).attr('disabled') !== 'undefined') {
        return;
      }
      var href  = $(this).attr('href');
      var box   = $(this);
      var boxes = $("[href='" + href + "']");
      Fetchx({
        url: href,
        type: 'POST',
        dataType: 'json',
        headers : {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
        },
        data: {
          value: $(this).attr('data-pass-value'),
        },
        beforeSend: function() {
          boxes.attr('disabled', true).addClass('disabled');
        },
        success: function(res) {
          if(typeof res.disabled !== 'undefined') {
            if(res.disabled) {
              $(boxes).attr('disabled', true).addClass('disabled');
              $(boxes).removeAttr('data-confirm');
            } else {
              $(boxes).removeAttr('disabled').removeClass('disabled');
            }
          } else {
            $(boxes).removeAttr('disabled').removeClass('disabled')
          }
          if(typeof res.class !== 'undefined') {
            $(boxes).addClass(res.class);
          }
          if(typeof res.redirect !== 'undefined') {
            window.location.href = res.redirect;
          }
          if(typeof res.refresh !== 'undefined') {
            if(res.refresh) {
              window.location.reload();
            }
          }
          if(typeof res.label !== 'undefined') {
            $(boxes).text(res.label);
          }
          if(!res.status) {
            return n.fire("Denegado", res.message, "error");
          } else {
            toastr.success(res.message, res.label, { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 2000 });
          }
          return res;
        },
        error: function() {
          toastr.error('Ocurrió un problema durante la ejecución', 'Ops!', { positionClass: 'toast-bottom-right', containerId: 'toast-bottom-right', 'timeOut': 2000 });
        }
      });
    });
  });
}
function render_editable() {
  $('[data-editable]').each(function(event ) {
    if(!_addTag(this, 'editable')) {
        return false;
    }
    var box = $(this);
    var xhr = $(this).attr('data-editable');
    $(this).removeAttr('data-editable').attr('data-editable-view', 1);
    var focus = false;
    enableImageResizeInDiv(this);
    $(this).attr('contenteditable', true);
    $(this).on('click', function() {
      if(focus) {
        return;
      }
      focus = true;
      if(this.tagName == 'INPUT') {
        if($(this).attr('type') == 'text') {
          document.execCommand('selectAll',false,null);
        }
      }
    });
    var stt = null;
    var stf = null;
    var svv = null;
    var is_div = this.tagName == 'DIV';
    var is_html = typeof $(this).attr('data-ishtml') !== 'undefined';
    var selected = is_html ? box.html() : (is_div ? box.text() : box.val());
    box.on((is_div ? 'input' : 'change'), function() {
      if(stt !== null) {
        clearTimeout(stt);
      }
      if(stf !== null) {
        box.removeClass('saved');
        clearTimeout(stf);
      }
      stt = setTimeout(function() {
        var input_01 = box.attr('data-input-01');
        var input_02 = box.attr('data-input-02');
        var tt = is_html ? box.html() : (is_div ? box.text() : box.val());
        console.log('ENVIAR', tt);
        if(tt === svv) {
          return;
        }
        svv = tt;
          Fetchx({
            title: 'Edición',
            url: xhr,
            headers : {
               'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
            },
            dataType: 'json',
            type: 'PUT',
            data: { value: tt, input_01: input_01, input_02: input_02 },
            complete: function() {
              box.addClass('saved');
              stf = setTimeout(function() {
                box.removeClass('saved');
              }, 1000);
            },
            success: function(data) {
              if(typeof data.redirect !== 'undefined') {
                window.location.href = data.redirect;
              }
              if(typeof data.refresh !== 'undefined') {
                if(data.refresh) {
                  window.location.reload();
                }
              }
              if(data.status) {
                if(is_html) {
                  box.html(data.value);

                } else if(is_div) {
                  box.text(data.value);
                }
                selected = tt;
                //toastSuccess()
                if(window.fn_trigger){
                  console.log("llamando funcion trigger")
                  fn_trigger();
                }
              } else {
                if(is_html) {
                  box.html(selected);

                } else if(is_div) {
                  box.text(selected);
                } else {
                  box.val(selected);
                }
                toastr.error(data.message);
              }
            },
            error: function() {
              if(is_html) {
                  box.html(selected);
                } else if(is_div) {
                  box.text(selected);
                } else {
                  box.val(selected);
                }
              toastr.error('Error');
            }
          });
      }, 1500);

    });
  });
}
function micro_ready() {
  console.log('init micro_ready');
  render_bootstrap();
  render_confirm_remove();
  render_link_confirm();
  render_confirm_input();
  render_dom_popup();
  render_input_time();
  render_autocomplete();
  render_input_ajax();
  render_select_default();
  render_editable();
  render_button_dinamic();
  render_block_dinamic();
  render_time_left();
}

$(document).ready(function () {
    micro_ready();
});
        function enableImageResizeInDiv(editor) {
            if (!(/chrome/i.test(navigator.userAgent) && /google/i.test(window.navigator.vendor))) {
                return;
            }
            var resizing = false;
            var currentImage;
            var createDOM = function (elementType, className, styles) {
                let ele = document.createElement(elementType);
                ele.className = className;
                setStyle(ele, styles);
                return ele;
            };
            var setStyle = function (ele, styles) {
                for (key in styles) {
                    ele.style[key] = styles[key];
                }
                return ele;
            };
            var removeResizeFrame = function () {
                document.querySelectorAll(".resize-frame,.resizer").forEach((item) => item.parentNode.removeChild(item));
            };
            var offset = function offset(el) {
                const rect = el.getBoundingClientRect(),
                scrollLeft = window.pageXOffset || document.documentElement.scrollLeft,
                scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                return { top: rect.top + scrollTop, left: rect.left + scrollLeft }
            };
            var clickImage = function (img) {
                removeResizeFrame();
                currentImage = img;
                const imgHeight = img.offsetHeight;
                const imgWidth = img.offsetWidth;
                const imgPosition = { top: img.offsetTop, left: img.offsetLeft };
                const editorScrollTop = editor.scrollTop;
                const editorScrollLeft = editor.scrollLeft;
                const top = imgPosition.top - editorScrollTop - 1;
                const left = imgPosition.left - editorScrollLeft - 1;
    
                editor.append(createDOM('span', 'resize-frame', {
                    margin: '10px',
                    position: 'absolute',
                    top: (top + imgHeight - 10) + 'px',
                    left: (left + imgWidth - 10) + 'px',
                    border: 'solid 3px blue',
                    width: '6px',
                    height: '6px',
                    cursor: 'se-resize',
                    zIndex: 1
                }));
    
                editor.append(createDOM('span', 'resizer top-border', {
                    position: 'absolute',
                    top: (top) + 'px',
                    left: (left) + 'px',
                    border: 'dashed 1px grey',
                    width: imgWidth + 'px',
                    height: '0px'
                }));
    
                editor.append(createDOM('span', 'resizer left-border', {
                    position: 'absolute',
                    top: (top) + 'px',
                    left: (left) + 'px',
                    border: 'dashed 1px grey',
                    width: '0px',
                    height: imgHeight + 'px'
                }));
    
                editor.append(createDOM('span', 'resizer right-border', {
                    position: 'absolute',
                    top: (top) + 'px',
                    left: (left + imgWidth) + 'px',
                    border: 'dashed 1px grey',
                    width: '0px',
                    height: imgHeight + 'px'
                }));
    
                editor.append(createDOM('span', 'resizer bottom-border', {
                    position: 'absolute',
                    top: (top + imgHeight) + 'px',
                    left: (left) + 'px',
                    border: 'dashed 1px grey',
                    width: imgWidth + 'px',
                    height: '0px'
                }));
    
                document.querySelector('.resize-frame').onmousedown = () => {
                    resizing = true;
                    return false;
                };
    
                editor.onmouseup = () => {
                    if (resizing) {
                        currentImage.style.width = document.querySelector('.top-border').offsetWidth + 'px';
                        currentImage.style.height = document.querySelector('.left-border').offsetHeight + 'px';
                        refresh();
                        currentImage.click();
                        resizing = false;
                    }
                };
    
                editor.onmousemove = (e) => {
                    if (currentImage && resizing) {
                        let height = e.pageY - offset(currentImage).top;
                        let width = e.pageX - offset(currentImage).left;
                        height = height < 1 ? 1 : height;
                        width = width < 1 ? 1 : width;
                        const top = imgPosition.top - editorScrollTop - 1;
                        const left = imgPosition.left - editorScrollLeft - 1;
                        setStyle(document.querySelector('.resize-frame'), {
                            top: (top + height - 10) + 'px',
                            left: (left + width - 10) + "px"
                        });
    
                        setStyle(document.querySelector('.top-border'), { width: width + "px" });
                        setStyle(document.querySelector('.left-border'), { height: height + "px" });
                        setStyle(document.querySelector('.right-border'), {
                            left: (left + width) + 'px',
                            height: height + "px"
                        });
                        setStyle(document.querySelector('.bottom-border'), {
                            top: (top + height) + 'px',
                            width: width + "px"
                        });
                    }
                    return false;
                };
            };
            var bindClickListener = function () {
                editor.querySelectorAll('img').forEach((img, i) => {
                    img.onclick = (e) => {
                        if (e.target === img) {
                            clickImage(img);
                        }
                    };
                });
            };
            var refresh = function () {
                bindClickListener();
                removeResizeFrame();
                if (!currentImage) {
                    return;
                }
                var img = currentImage;
                var imgHeight = img.offsetHeight;
                var imgWidth = img.offsetWidth;
                var imgPosition = { top: img.offsetTop, left: img.offsetLeft };
                var editorScrollTop = editor.scrollTop;
                var editorScrollLeft = editor.scrollLeft;
                const top = imgPosition.top - editorScrollTop - 1;
                const left = imgPosition.left - editorScrollLeft - 1;
    
                editor.append(createDOM('span', 'resize-frame', {
                    position: 'absolute',
                    top: (top + imgHeight) + 'px',
                    left: (left + imgWidth) + 'px',
                    border: 'solid 2px red',
                    width: '6px',
                    height: '6px',
                    cursor: 'se-resize',
                    zIndex: 1
                }));
    
                editor.append(createDOM('span', 'resizer', {
                    position: 'absolute',
                    top: (top) + 'px',
                    left: (left) + 'px',
                    border: 'dashed 1px grey',
                    width: imgWidth + 'px',
                    height: '0px'
                }));
    
                editor.append(createDOM('span', 'resizer', {
                    position: 'absolute',
                    top: (top) + 'px',
                    left: (left + imgWidth) + 'px',
                    border: 'dashed 1px grey',
                    width: '0px',
                    height: imgHeight + 'px'
                }));
    
                editor.append(createDOM('span', 'resizer', {
                    position: 'absolute',
                    top: (top + imgHeight) + 'px',
                    left: (left) + 'px',
                    border: 'dashed 1px grey',
                    width: imgWidth + 'px',
                    height: '0px'
                }));
            };
            var reset = function () {
                if (currentImage != null) {
                    currentImage = null;
                    resizing = false;
                    removeResizeFrame();
                }
                bindClickListener();
            };
            editor.addEventListener('scroll', function () {
                reset();
            }, false);
            editor.addEventListener('mouseup', function (e) {
                if (!resizing) {
                    const x = (e.x) ? e.x : e.clientX;
                    const y = (e.y) ? e.y : e.clientY;
                    let mouseUpElement = document.elementFromPoint(x, y);
                    if (mouseUpElement) {
                        let matchingElement = null;
                        if (mouseUpElement.tagName === 'IMG') {
                            matchingElement = mouseUpElement;
                        }
                        if (!matchingElement) {
                            reset();
                        } else {
                            clickImage(matchingElement);
                        }
                    }
                }
            });
        }
function convertirMilisegundos(t, vms) {
  ms = t % 1000;
  t = parseInt((t - ms) / 1000);
  ss = t % 60;
  t = parseInt((t - ss) / 60);
  mm = t % 60;
  hh = parseInt((t - mm) / 60);
  return hh.toString().padStart(2, "0") + ":" + mm.toString().padStart(2, "0") + ":" + ss.toString().padStart(2, "0") + (vms || ms > 0 ? "." + ms.toString().padStart(3, "0") : "");
}

function operarMilisegundos(t1, t2, op) {
  var tt = "";
  var expTiempo = /((\d{1,2})\:)?(\d{1,2})\:(\d{2})(\.(\d{1,3}))?/;

  r1 = t1.match(expTiempo);
  r2 = t2.match(expTiempo);

  if(r1 && r2) {
    hh1 = r1[2] ? parseInt(r1[2]) : 0;
    mm1 = r1[3] ? parseInt(r1[3]) : 0;
    ss1 = r1[4] ? parseInt(r1[4]) : 0;
    ms1 = r1[6] ? parseInt(r1[6].padEnd(3, "0")) : 0;
    tt1 = hh1 * 60 * 60 * 1000 + mm1 * 60 * 1000 + ss1 * 1000 + ms1;

    hh2 = r2[2] ? parseInt(r2[2]) : 0;
    mm2 = r2[3] ? parseInt(r2[3]) : 0;
    ss2 = r2[4] ? parseInt(r2[4]) : 0;
    ms2 = r2[6] ? parseInt(r2[6].padEnd(3, "0")) : 0;
    tt2 = hh2 * 60 * 60 * 1000 + mm2 * 60 * 1000 + ss2 * 1000 + ms2;

    vms = r1[6] || r2[6];
    if(op == '+') {
      tt = convertirMilisegundos(tt1 + tt2, vms);
    }
    else if(op == '-') {
      tt = convertirMilisegundos(tt1 - tt2, vms);
    }
  }

  return tt;
}

(function () {
    var espacio = null;
    var actual = null;
    var saving = false;
    var openModaly = function () {
        if (typeof $(espacio).attr('data-modaly-id') === 'undefined' || $(espacio).attr('data-modaly-id') == "") {
            espacio = null;
            console.log('openModaly NULl');
            return false;
        }
        console.log('openModaly:', espacio);
        actual = $(espacio).html();
        var bloque = $("<div>").addClass('bloqueModificable');
        Fetchx({
          title: 'Habilitando EdiciÃ³n',
            id: 'save-punto',
            delay: 0,
            loading: '#loading_calendar',
            url: $(espacio).attr('data-modaly'),
            type: 'GET',
            data: {
                id: $(espacio).attr('data-modaly-id'),
            },
            success: function (res) {
                $(espacio).html(bloque.html(res));
                if (bloque.find('.combo-extra').length == 0) {
                    bloque.find('.combo').val(actual);
                }
            },
            error: function () {
                alert('Ha ocurrido un problema en el sistema');
                saving = false;
                closeModaly();
            }
        });
    };
    var closeModaly = function () {
        console.log('closeModaly');
        if (!saving) {
            $(espacio).html(actual);
            espacio = null;
        }
    };
    var saveModaly = function () {
        saving = true;
        $(espacio).find('.bloqueModificable').slideUp(400, function () {
            if (saving) {
                $(espacio).html('...');
            }
        });
        var serial = $(espacio).find('.tipFormulario').serialize();
        serial += '&_token=' + $("[name='_token']").val() + '&id=' + $(espacio).attr('data-modaly-id');
        Fetchx({
          title: 'Edición',
            id: 'save-punto',
            delay: 0,
            loading: '#loading_calendar',
            url: $(espacio).attr('data-modaly'),
            type: 'POST',
            dataType: 'json',
            data: serial,
            success: function (res) {
                if(res.status) {
                    if(typeof res.data.respuesta !== 'undefined') {
                        $(espacio).text(res.data.respuesta);
                    }
                    saving = false;
                    espacio = null;
                    typeof calcular_costos !== 'undefined' && calcular_costos();
                    if (typeof res.refresh !== "undefined" && res.refresh) {
                        setTimeout(function () {
                            location.reload();
                        }, 2000);
                    }
                } else {
                    alert(res.message);
                }
            },
            error: function () {
                alert('Ha ocurrido un problema en el sistema');
                saving = false;
                closeModaly();
            }
        });
    }
    window.modalyOpen = function(box, url) {
        if (espacio === null) {
            espacio = $(box);
            $(espacio).attr('data-modaly', url);
            openModaly();
        } else {
            closeModaly();
        }
    }
    $(document).on('click', '[data-modaly]', function (e) {
        e.stopPropagation();
        if (espacio === null) {
            espacio = this;
            openModaly();
        } else {
            closeModaly();
        }
    });
    $(document).on('click', '[data-modaly] .bloqueModificable', function (e) {
        e.stopPropagation();
    });
    $(document).on('submit', '[data-modaly] .bloqueModificable .tipFormulario', function (e) {
        e.preventDefault();
        saveModaly();
    });
    $(document).on('click', function () {
        if (espacio !== null) {
            closeModaly();
        }
    });
})();
