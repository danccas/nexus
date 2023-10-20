function Bucketjs () {
  var _instance = this;
  var elements = {};
  var commit = [];
  var _element = null;
  var bucket_path = null;

  var validateFile = function (file) {
    var validTypes = [
      "image/jpeg",
      "image/png",
      "image/gif",
      "application/pdf",
      "application/msword",
      "application/vnd.ms-powerpoint",
      "application/vnd.ms-excel",
      "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
      "application/vnd.openxmlformats-officedocument.presentationml.presentation",
      "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
    ];
    if (validTypes.indexOf(file.type) === -1) {
      alert("El archivo tiene un formato no permitido: " + file.name);
      return false;
    }
    var maxSizeInBytes = 10e7; // 10MB
    if (file.size > maxSizeInBytes) {
      alert("El archivo es muy pesado: " + file.name);
      return false;
    }
    return true;
  };
  var handleFiles = function (files) {
    if (files.length === 0) {
      return false;
    }
    for (var i = 0, len = files.length; i < len; i++) {
      if (validateFile(files[i])) {
        commitBucket(files[i]);
      }
    }
    pushBucket();
  };
  var commitBucket = function (file) {
    commit.push(file);
  };
  var pushBucket = function () {
    if (commit.length === 0) {
      return false;
    }
    var formData = new FormData();
    formData.append("path", bucket_path);
    for (var i = 0, len = commit.length; i < len; i++) {
      formData.append("files[]", commit[i]);
    }
    commit = [];
    $(_element)
      .find(".bucket-loading")
      .attr("data-loading", "push")
      .slideDown();
    var ajax = new XMLHttpRequest();
    ajax.onreadystatechange = function (e) {
      if (ajax.readyState === 4) {
        if (ajax.status === 200) {
          goPath(bucket_path);
          //pullBucket();
        } else {
          alert("Ha ocurrido un error inesperado");
          //pullBucket();
        }
      }
    };
    ajax.upload.onprogress = function (evt) {
      if (evt.lengthComputable) {
        var percentComplete = parseInt((evt.loaded / evt.total) * 100);
        console.log("Upload: " + percentComplete + "% complete");
        $(_element)
        .find(".bucket-avance")
        .animate({
          height: percentComplete + "%",
        }, 50)
        .attr("data-porcen", percentComplete + "%");
      }
    };
    ajax.open("POST", "/documentos/ajax/upload", true);
    ajax.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').getAttribute("content"));
    ajax.send(formData);
  };
  var breadDir = function() {

  };

  var renderElement = function (box) {
    _element = box;
    var can_upload = $(_element).attr('data-upload') || false;

    can_upload = can_upload === 'true';
    if (typeof _element !== "undefined") {
      if (!$(_element).is(":visible")) {
        //$(_element).remove();
      }
    }
    let preventDefaults = function (e) {
      e.preventDefault()
      e.stopPropagation()
    };
    let dropArea = box;
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
      dropArea.addEventListener(eventName, preventDefaults, false)   
      document.body.addEventListener(eventName, preventDefaults, false)
    });
    box.addEventListener('dragenter', function(e) {
      $(box).addClass('ready_for_upload');
    });
    box.addEventListener('dragleave', function(e) {
      $(box).removeClass('ready_for_upload');
    });
    box.addEventListener('dragover', function(e) {
      $(box).addClass('ready_for_upload');
    });
    box.addEventListener('drop', function(e) {
      console.log('drop', e);
      var dt = e.dataTransfer;
      var files = dt.files;
      handleFiles(files);
    }, false);
    $(box).addClass("bucket bucket-initial");
    $(box).append($("<div>").addClass('bucket-navigation').append($('<div>').text('/').addClass('bar')).append($('<div>').addClass('goto').text('Ir')));
    $(box).append($("<div>").addClass('bucket-options')
      .append('<a href="javascript:void(0);" class="bucketOptionExpediente">Crear Expediente</a>')
      .append('<a href="javascript:void(0);" class="bucketOptionDirectory">Crear Carpeta</a>')
      .append('<a href="javascript:void(0);" class="bucketOptionUpload">Subir Archivo</a>')
    );
//    $(box).append($("<div>").addClass('bucket-space-upload').text('Subir Archivo'));
    $(box).append($('<div>').addClass('bucket-table-content').html($("<table>").addClass('table table-sm').css({width: '100%'})
      .append($("<thead>").html("<tr><th colspan='2'>Archivo</th><th>Descripción</th><th>Páginas</th><th>Tamaño</th><th>Propietario</th><th>Subido el</th><th></th></tr>"))
      .append($("<tbody>").addClass('StackedListDrag').attr('data-container', 'repository').attr('data-dropzone', 'draw'))));
    box.bucket_path = bucket_path;
//    $(box).removeAttr("data-path");
    $(box).on('click', '.bucket-button-full', function() {
      //$(box).toggleClass('bucket-full');
    });
    $(box).on('click', ".goto", function() {
      let dir = prompt('Ingrese la ruta:', bucket_path);
      if(!dir) {
        return false;
      }
      if(!(/^[a-zA-Z0-9\-\/]+$/.test(dir))) {
        alert('Ingrese un nombre correcto.(Numeros, letras, Guión)');
        return $(this).click();
      }
      goPath(dir);
    });
    if(can_upload) {
      $(box).on('click', ".bucketOptionExpediente", function(e) {
        let dir = prompt('Ingrese el nombre del Expediente');
        let oid = $(_element).attr('data-oid');
        let cid = $(_element).attr('data-cid');
        if(!dir) {
          return false;
        }
        if(!(/^[a-zA-Z0-9\-\ ]+$/.test(dir))) {
          alert('Ingrese un nombre correcto.(Numeros, letras, Guión)');
          return $(this).click();
        }
        Fetchx({
          id: "bucket-create-expediente",
          delay: 50,
          loading: $("[data-loading='pull'][data-id='" + 0 + "']"),
          url: "/documentos/crearExpediente",
          data: { path: bucket_path, name: dir, oid: oid, cid: cid },
          type: "POST",
          headers : {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
          },
          dataType: "json",
          success: function (res) {
            goPath(bucket_path);
          },
          complete: function () {
            $(_element).find("[data-loading='pull']")
              .removeAttr("data-loading");
          },
        });
      });
      $(box).on('click', ".bucketOptionDirectory", function(e) {
        let dir = prompt('Ingrese el nombre de la carpeta');
        if(!dir) {
          return false;
        }
        if(!(/^[a-zA-Z0-9\-\ ]+$/.test(dir))) {
          alert('Ingrese un nombre correcto.(Numeros, letras, Guión)');
          return $(this).click();
        }
        goPath(bucket_path + '/' + dir);
      });
      $(box).on('click', ".bucketOptionUpload", function (e) {
        if (e.target !== this) {
          return false;
        }
        e.preventDefault();
        if (!$(box).hasClass("bucket-ready")) {
          return false;
        }
        var fakeInput = document.createElement("input");
        fakeInput.type = "file";
        fakeInput.multiple = true;
        fakeInput.click();
        fakeInput.addEventListener("change", function () {
          var files = fakeInput.files;
          console.log("UPLOADES", files);
          handleFiles(files);
        });
        console.log("Subir archivo!");
        return false;
      });
    }
    typeof _instance.__event_onRender !== 'undefined' && _instance.__event_onRender();
  };
  var goPath = function(path) {
    bucket_path = path ||  '/';
    bucket_path = bucket_path.replace(/^\/+|\/+$/gm,'');
    _element.bucket_path = bucket_path;

    var acumulado = '';
    var html = $('<div>');
    bucket_path.split('/').forEach(function (item) {
      acumulado += '/' + item;
      html.append($('<span>').text('/'));
      html.append($('<a>').attr('href', 'javascript:void(0)').attr('data-path', acumulado).text(item).on('click', function() {
        goPath($(this).attr('data-path'));
      }));
    });
    $(_element).find('.bucket-navigation>div.bar').html(html);
    pullFiles();
  };
  var fillFile = function (file) {
    var ul = $(_element).find("tbody");
    if(file.tipo == 1 || file.tipo == 2 || file.tipo == 4 || (file.tipo == 3 && file.download)) {
      ul.append(
        $("<tr>")
          .addClass(file.id ? 'StackedListItem--isDraggable' : 'es_directorio')
          .attr('data-id', file.id)
          .attr('data-plantilla', file.plantilla ? 'true' : 'false')
          .attr("data-download", file.download)
          .append($("<td>").html('<i style="font-size:11px;" class="bx bxs-' + (file.is_file ? 'file' : 'folder') + '"></i>'))
          .append($("<td>").addClass('text-left rotulo').text(file.name ?? 'En desarrollo').on('click', function() {
            if(!file.download) {
              return false;
            }
            if(file.tipo == 1) {
              goPath(file.download);
            } else {
              window.open($(this).closest('tr').attr("data-download"));
            }
            }))
          .append($("<td>").addClass('rotulo').text(file.rotulo))
          .append($("<td>").addClass("foliox").text(file.folio))
          .append($("<td>").addClass("size").text(file.size))
          .append($("<td>").addClass("uploaded").text(file.created_by))
          .append($("<td>").addClass("uploaded").text(file.created_on).attr('title','Subido el:'))
          .append($("<td>").addClass("delete").text('Eliminar').attr('title','Acción no disponible').on('click', function() {
            if(!file.download) {
              return false;
            }
						var li = $(this).closest('li');
						var li_id = li.attr('data-id');
						li.slideUp();
						Fetchx({
              id: "delete" + li_id,
              url: "/bucket/delete",
              type: "POST",
              data: { id: li_id },
              dataType: "json",
              complete: function (data) {
                    console.log("Adios");
                },
            });
						console.log('Eliminar', li.attr('data-id'));
					}))
      );
    } else {
      ul.append(
        $("<tr>")
          .attr("data-download", file.download)
          .append($("<td>").html('<i style="font-size:11px;" class="bx bxs-file"></i>'))
          .append($("<td>").addClass('text-left rotulo').html('<span style="color: #0c7200;">En desarrollo</span>').on('click', function() {
            let url = '/documentos/' + file.id + '/expediente/inicio';
            window.open(url, '_blank').focus();
          }))
          .append($("<td>").addClass('rotulo').text(file.rotulo))
          .append($("<td>").addClass("foliox").text(file.folio))
          .append($("<td>").addClass("size").text('...'))
          .append($("<td>").addClass("uploaded").text(file.created_by))
          .append($("<td>").addClass("uploaded").text(file.created_on).attr('title','Subido el:'))
          .append($("<td>").addClass("delete"))
      );
    }
  };
  var pullFiles = function () {
      $(_element)
        .removeClass("bucket-initial")
        .addClass("bucket-ready");

        $(_element).prepend(
          $("<div>")
            .addClass("bucket-loading")
            .html(
              '<div class="bucket-barra"><div class="bucket-avance" data-porcen="0%"></div></div>'
            )
        );
      $(_element)
        .find(".bucket-loading")
        .attr("data-loading", "pull");
    
    Fetchx({
      id: "bucket-pull-" + 0,
      delay: 50,
      loading: $("[data-loading='pull'][data-id='" + 0 + "']"),
      url: "/documentos/ajax/get",
      data: { path: bucket_path },
      type: "POST",
      headers : {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content")
      },
      dataType: "json",
      success: function (res) {
        $(_element).find("tbody").empty();
        $.each(res.data, function (k, n) {
          fillFile(n);
        });
      },
      complete: function () {
        $(_element).find("[data-loading='pull']")
          .removeAttr("data-loading");
      },
    });
  };
  return {
    capture: function (box) {
      renderElement(box);
      console.log("Bucketjs-capture", box);
      goPath($(box).attr('data-path') ||  '/');
    },
    getElements: function () {
      return elements;
    },
    onRender: function (cb) {
      _instance.__event_onRender = cb;
    }
  };
};
