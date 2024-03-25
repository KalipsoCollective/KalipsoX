String.prototype.hashCode = function () {
  var hash = 0,
    i,
    chr;
  if (this.length === 0) return hash;
  for (i = 0; i < this.length; i++) {
    chr = this.charCodeAt(i);
    hash = (hash << 5) - hash + chr;
    hash |= 0; // Convert to 32bit integer
  }
  return hash;
};

class KalipsoXJS {
  // version
  version = "1.0.0";
  lang = "en";
  langDefinitions = {
    en: {
      buttons: {
        colvis: "Change columns",
        copy: "Copy",
        copyTitle: "Copied to clipboard",
        copySuccess: {
          _: "%d rows copied",
          1: "1 row copied",
        },
        print: "Print",
      },
      export: "Export",
      all: "All",
    },
    tr: {
      buttons: {
        colvis: "Sütunları değiştir",
        copy: "Kopyala",
        copyTitle: "Panoya kopyalandı",
        copySuccess: {
          _: "%d satır kopyalandı",
          1: "1 satır kopyalandı",
        },
        print: "Yazdır",
      },
      export: "Dışa Aktar",
      all: "Hepsi",
    },
  };
  heartBeatInterval = 30000;

  constructor() {
    this.init();
    return this;
  }

  init(firstLoad = true, pageSync = false) {
    // color scheme
    if (localStorage.getItem("kx_theme")) {
      this.changeColorScheme(localStorage.getItem("kx_theme"));
    } else {
      if (window.matchMedia && window.matchMedia("(prefers-color-scheme: dark)").matches) {
        this.changeColorScheme("dark");
      } else {
        this.changeColorScheme("light");
      }
    }

    if (document.fullscreenElement) {
      $('[data-kx-action="fullscreen"] i.ti').removeClass("ti-maximize").addClass("ti-minimize");
    } else {
      $('[data-kx-action="fullscreen"] i.ti').removeClass("ti-minimize").addClass("ti-maximize");
    }

    if (firstLoad) {
      this.dateTime = luxon.DateTime;
      console.info("KalipsoXJS v" + this.version + " initialized!");
      this.lang = document.documentElement.lang;

      // scroll to top
      $("html, body").animate({ scrollTop: 0 }, "slow");

      // heart beat
      if ($("body").hasClass("dashboard")) {
        this.startHeartbeat();
      }

      // front init
      if (typeof window.frontInit === "function") {
        window.frontInit();
      }

      setTimeout(() => {
        NProgress.done();
      }, 250);

      $("time.timeago").timeago();
    }

    // Event listeners
    $(document).off("click", "[data-kx-action]");
    $(document).on("click", "[data-kx-action]", async (e) => {
      e.preventDefault();
      e.stopPropagation();
      const el = e.target;
      const action = el.getAttribute("data-kx-action");

      if (el.getAttribute("data-kx-again") !== null) {
        if (el.getAttribute("data-kx-again") !== "waiting") {
          el.setAttribute("data-kx-again", "waiting");
          el.querySelector("i.ti").classList.value = el.querySelector("i.ti").classList.value.replace("ti-", "ti--");
          el.querySelector("i.ti").classList.add("ti-question-mark");
          setTimeout(() => {
            el.setAttribute("data-kx-again", "");
            el.querySelector("i.ti").classList.value = el.querySelector("i.ti").classList.value.replace("ti--", "ti-");
            el.querySelector("i.ti").classList.remove("ti-question-mark");
          }, 3000);
          return;
        }
      }

      switch (action) {
        case "toggle_theme":
          const newTheme = document.body.getAttribute("data-bs-theme") === "light" ? "dark" : "light";
          this.changeColorScheme(newTheme);
          if (el.querySelector("i.ti")) {
            el.querySelector("i.ti").setAttribute("class", "ti");
            el.querySelector("i.ti").classList.add(newTheme === "dark" ? "ti-sun" : "ti-moon");
          }
          break;
        case "show_password":
          const inputParent = this.findParent(el, ".input-group");
          if (inputParent) {
            const input = inputParent.querySelector("input");
            if (input.type === "password") {
              input.type = "text";
            } else {
              input.type = "password";
            }
          }
          break;

        case "set_new_password":
          $('[data-kx-action="set_new_password"]').addClass("d-none");
          $(".set-new-password").removeClass("d-none");
          break;

        case "switch_trigger":
          const selector = $(el).attr("data-kx-selector");
          let switchParent = this.findParent(el, "form");
          if (selector && switchParent) {
            $(switchParent).find(selector).prop("checked", !$(switchParent).find(selector).prop("checked"));
          }
          break;
        case "fullscreen":
          if (document.fullscreenElement) {
            document.exitFullscreen();
            $("i", el).removeClass("ti-minimize").addClass("ti-maximize");
          } else {
            document.documentElement.requestFullscreen();
            $("i", el).removeClass("ti-maximize").addClass("ti-minimize");
          }
          break;
        case "refresh":
          // with pjax
          if (el.getAttribute("data-kx-pjax") === "true") {
            $.pjax.reload("body");
          } else {
            window.location.reload();
          }
          break;
        default:
          // direct send request
          const data = el.getAttribute("data-kx-body") ? JSON.parse(el.getAttribute("data-kx-body")) : {};
          const res = await this.sendRequest(action, "POST", data);
          this.pullResponse(res);
          break;
      }
    });

    const dateBirthPicker = flatpickr(".date-birth", {
      dateFormat: "Y-m-d",
      altInput: true,
      altFormat: "F j, Y",
      minDate: this.dateTime.local().minus({ years: 75 }).toISODate(),
      maxDate: this.dateTime.local().minus({ years: 15 }).toISODate(),
      locale: this.lang,
    });
    $("time.timeago").each((i, el) => {
      if ($(el).timeago) {
        $(el).timeago("update", $(el).attr("datetime"));
      } else {
        $(el).timeago();
      }
    });

    // Form submit
    $('form[data-kx-form]:not([data-kx-form="direct"])').each((i, form) => {
      $(form).off("submit");
      $(form).on("submit", async (event) => {
        event.preventDefault();
        await this.sendForm(event);
      });
    });

    // bootstrap tooltips
    $("[data-bs-toggle='tooltip']").each((i, el) => {
      bootstrap.Tooltip.getOrCreateInstance(el);
    });

    // datatables
    if ($.fn.DataTable) {
      this.prepareDatatables();
    }

    if (pageSync) {
      if ($("body").hasClass("dashboard")) {
        this.heartBeat();
      }
    }
  }

  async sendForm(event) {
    // find submit button
    const submitButton = event.target.querySelector('button[type="submit"]') || document.querySelector('[form="' + event.target.getAttribute("id") + '"]');
    if (submitButton) {
      $(submitButton).addClass("disabled loading");
    }
    const form = $(event.target);

    // find all inputs and remove invalid class
    form.find("input, select, textarea").removeClass("is-invalid is-valid");
    // find all invalid feedbacks and remove them
    form.find(".invalid-feedback").text("");

    const url = form.attr("action");
    const method = form.attr("method");
    const data = [...new FormData(form[0])].reduce((obj, [key, val]) => {
      // array or multidimensional object
      if (key.includes("[]")) {
        // array
        key = key.replace("[]", "");
        if (obj[key] !== undefined) {
          if (!Array.isArray(obj[key])) {
            obj[key] = [obj[key]];
          }
          obj[key].push(val);
        } else {
          obj[key] = val;
        }
        return obj;
      } else if (key.includes("[") && key.includes("]")) {
        // multidimensional object
        const keys = key.split(/\[|\]/).filter((k) => k);
        let last = keys.pop();
        let temp = obj;
        keys.forEach((k) => (temp = temp[k] = temp[k] || {}));
        temp[last] = val;
        return obj;
      } else {
        // direct value
        obj[key] = val;
      }
      return obj;
    }, {});
    const response = await this.sendRequest(url, method, data);
    setTimeout(() => {
      if (submitButton) {
        $(submitButton).removeClass("disabled loading");
      }
    }, 500);
    this.pullResponse(response, event.target);
  }

  async sendRequest(url = null, method = "POST", data = {}, progressBar = true) {
    if (progressBar) {
      NProgress.inc();
    }
    url = url ?? window.location.href;

    method = method ?? "POST";
    method = method.toUpperCase();

    data = typeof data === "string" ? JSON.parse(data) : data;
    data = typeof data === "object" ? data : {};

    let headers = {};

    // X-Language
    headers["X-Language"] = this.lang;

    let fetchOptions = {
      method: method,
      mode: "cors",
      cache: "no-cache",
      headers: headers,
      referrerPolicy: "same-origin",
      redirect: "follow",
    };
    if (method !== "GET" && method !== "HEAD") {
      let formData;

      fetchOptions.headers["Accept"] = "application/json";

      const resursiveFormData = (obj, formData = new FormData(), parentKey = null) => {
        for (const key in obj) {
          if (obj.hasOwnProperty(key)) {
            const value = obj[key];
            const formKey = parentKey ? `${parentKey}[${key}]` : key;

            if (typeof value === "object" && !(value instanceof File)) {
              resursiveFormData(value, formData, formKey);
            } else if (value instanceof FileList) {
              for (let i = 0; i < value.length; i++) {
                formData.append(formKey, value[i]);
              }
            } else if (value instanceof File) {
              formData.append(formKey, value);
            } else {
              formData.append(formKey, value);
            }
          }
        }
        return formData;
      };
      formData = resursiveFormData(data);

      fetchOptions.body = formData;
    } else if (method === "GET" && data) {
      let params = new URLSearchParams(data);
      url += "?" + params.toString();
    }

    const ret = await fetch(url, fetchOptions)
      .then((response) => {
        if (response.status >= 200 && response.status < 300) {
          return response.json();
        } else {
          throw new Error(response.statusText);
        }
      })
      .then((data) => {
        return data;
      })
      .catch((error) => {
        let message = "";
        console.log("A problem occurred!", error);
        if (url.includes("auth/heartbeat") && window.kxHeartbeat) {
          clearInterval(window.kxHeartbeat);
        }
        if (typeof error.message === "string") {
          return error.message;
        } else {
          return error;
        }
        /*
        this.notify(
          "A problem occurred!" + (message === "" ? "" : "(" + message + ")"),
          "error"
        ); */
      });
    if (progressBar) {
      NProgress.done();
    }
    return ret;
  }

  checkHtmlStringsEqual(str1, str2) {
    var $div1 = $("<div>").html(str1);
    var $div2 = $("<div>").html(str2);

    return $div1.text() === $div2.text();
  }

  pullResponse(data, form = null) {
    if (form === null) {
      form = $("form");
    }
    if (data && typeof data === "object") {
      // notify
      if (typeof data.notify !== "undefined" && data.notify.length > 0) {
        data.notify.forEach((alert) => {
          this.notify(alert.message, alert.type);
        });
      }

      // dom manipulation
      if (typeof data.dom !== "undefined" && Object.keys(data.dom).length > 0) {
        for (const [selector, manipulationData] of Object.entries(data.dom)) {
          if ($(selector)) {
            for (const [key, value] of Object.entries(manipulationData)) {
              switch (key) {
                case "html":
                  let currentHtml = $(selector).html();
                  if (currentHtml.replace(/\s/g, "") !== value.replace(/\s/g, "")) {
                    $(selector).html(value);
                  }
                  break;
                case "text":
                  let currentText = $(selector).text();
                  if (currentText !== value) {
                    $(selector).text(value);
                  }
                  break;
                case "append":
                  $(selector).append(value);
                  break;
                case "prepend":
                  $(selector).prepend(value);
                  break;
                case "after":
                  $(selector).after(value);
                  break;
                case "before":
                  $(selector).before(value);
                  break;
                case "remove":
                  $(selector).remove();
                  break;
                case "addClass":
                  if (!$(selector).hasClass(value)) {
                    $(selector).addClass(value);
                  }
                  break;
                case "removeClass":
                  if ($(selector).hasClass(value)) {
                    $(selector).removeClass(value);
                  }
                  break;
                case "toggleClass":
                  $(selector).toggleClass(value);
                  break;
                case "attr":
                  for (const [attr, val] of Object.entries(value)) {
                    $(selector).attr(attr, val);
                  }
                  break;
                case "removeAttr":
                  $(selector).removeAttr(value);
                  break;
                case "val":
                  $(selector).val(value);
                  break;
                case "prop":
                  for (const [prop, val] of Object.entries(value)) {
                    $(selector).prop(prop, val);
                  }
                  break;
                case "removeProp":
                  $(selector).removeProp(value);
                  break;
                case "css":
                  for (const [prop, val] of Object.entries(value)) {
                    $(selector).css(prop, val);
                  }
                  break;
                case "data":
                  for (const [prop, val] of Object.entries(value)) {
                    $(selector).data(prop, val);
                  }
                  break;
                default:
                  break;
              }
            }
          }
        }
      }

      // redirect
      if (typeof data.redirect !== "undefined") {
        const url = new URL(data.redirect.url);
        if (url.hostname !== window.location.hostname) {
          setTimeout(() => {
            window.open(data.redirect.url, "_blank").opener = null;
          }, data.redirect.time || 0);
        } else {
          setTimeout(() => {
            if (data.redirect.url === window.location.href) {
              if (typeof data.redirect.direct !== "undefined" && data.redirect.direct === true) {
                window.location.reload();
              } else {
                $.pjax.reload();
              }
            } else {
              if (typeof data.redirect.direct !== "undefined" && data.redirect.direct === true) {
                window.location.href = data.redirect.url;
              } else {
                $.pjax({ url: data.redirect.url, container: "body" });
              }
            }
          }, data.redirect.time || 0);
        }
      }

      // modal hide
      if (typeof data.modal_hide !== "undefined" && data.modal_hide) {
        $(data.modal_hide).modal("hide");
      }

      // modal show
      if (typeof data.modal_show !== "undefined" && data.modal_show) {
        $(data.modal_show).modal("show");
      }

      // table reload
      if (typeof data.table_reload !== "undefined" && data.table_reload && window.kxTables) {
        if (window.kxTables[data.table_reload]) {
          window.kxTables[data.table_reload].ajax.reload();
        }
      }

      // form reset
      if (typeof data.form_reset !== "undefined" && data.form_reset === true && form) {
        $(form).trigger("reset");
      }

      // form validation
      if (typeof data.heart_beat_stop !== "undefined" && data.heart_beat_stop === true) {
        clearInterval(window.kxHeartbeat);
      }

      // heart beat direct
      if (typeof data.heart_beat_direct !== "undefined" && data.heart_beat_direct) {
        this.heartBeat();
      }
    }
    this.init(false);
  }

  changeColorScheme(color = "light") {
    if (!["light", "dark"].includes(color)) {
      color = "light";
    }
    document.body.setAttribute("data-bs-theme", color);
    localStorage.setItem("kx_theme", color);
    if (color === "dark") {
      document.documentElement.classList.add("dark");
    }

    if ($('[data-kx-action="toggle_theme"] i.ti')) {
      $('[data-kx-action="toggle_theme"] i.ti').removeClass("ti-moon ti-sun");
      $('[data-kx-action="toggle_theme"] i.ti').addClass(color === "dark" ? "ti-sun" : "ti-moon");
    }
  }

  findParent(el, tagClassOrId) {
    let parent = el.parentElement;
    while (parent) {
      if (parent.tagName === tagClassOrId.toUpperCase()) {
        return parent;
      }
      if (parent.classList.contains(tagClassOrId.replace(".", ""))) {
        return parent;
      }
      if (parent.id === tagClassOrId.replace("#", "")) {
        return parent;
      }
      parent = parent.parentElement;
    }
    return null;
  }

  notify(message, type = "info", duration = 3000, close = true) {
    Toastify({
      text: message,
      close: close,
      duration: duration,
      gravity: "top",
      position: "right",
      stopOnFocus: true,
      className: "toastn " + type,
      escapeMarkup: false,
    }).showToast();
  }

  async heartBeat() {
    const response = await this.sendRequest("/auth/heartbeat", "POST", {}, false);
    if (response && typeof response === "object") {
      this.pullResponse(response);
      if (response.heartBeatStop) {
        clearInterval(window.kxHeartbeat);
      }
    }
  }

  async startHeartbeat() {
    const heartbeat = () => {
      this.heartBeat();
    };
    this.heartBeat();
    if (window.kxHeartbeat === undefined) {
      window.kxHeartbeat = setInterval(heartbeat, this.heartBeatInterval);
    }
  }

  prepareDatatables() {
    $.fn.dataTable.ext.errMode = "none";

    if (window.kxTables === undefined) {
      window.kxTables = {};
    }

    $("[data-kx-table]").each((i, table) => {
      const tableKey = table.getAttribute("data-kx-table");
      if (window.kxTables[tableKey]) {
        // window.kxTables[tableKey].draw();
      }
      const options = {
        processing: true,
        serverSide: true,
        // responsive: true,
        deferRender: true,
        stateSave: true,
        stateDuration: 0,
        stateLoadParams: function (settings, data) {
          for (let i = 0; i < data.columns.length; i++) {
            if (typeof data.columns[i].search !== "undefined") {
              let colSearchVal = data.columns[i].search.search;
              if (colSearchVal !== "") {
                $("input, select", $("#" + settings.sTableId + " tfoot th")[i])
                  .val(colSearchVal)
                  .change();
              }
            }
          }
        },
        /*
        dom:
          "<'row'<'col-sm-6'l><'col-sm-6 text-right'B>f>" +
          "<'row'<'col-sm-12'<'table-responsive'tr>>>" +
          "<'row'<'col-sm-5'i><'col-sm-7'p>>",
        */
        dom: "<'row'<'col-sm-6'l><'col-sm-6'B>><'row'<'col-12'<'table-responsive'tr>>> <'row'<'col-md-5'i><'col-md-7'p>>",
        buttons: [
          {
            extend: "colvis",
            className: "btn-sm btn-primary",
          },
          {
            extend: "collection",
            text: this.langDefinitions[this.lang].export,
            className: "btn-sm btn-primary",
            buttons: [
              {
                extend: "copyHtml5",
                charset: "utf-8",
                exportOptions: {
                  columns: ":visible",
                },
              },
              {
                extend: "excelHtml5",
                charset: "utf-8",
                exportOptions: {
                  columns: ":visible",
                },
              },
              {
                extend: "csvHtml5",
                charset: "utf-8",
                exportOptions: {
                  columns: ":visible",
                },
              },
              {
                extend: "print",
              },
            ],
          },
        ],
        lengthMenu: [
          [10, 25, 50, 100, -1],
          [10, 25, 50, 100, this.langDefinitions[this.lang].all],
        ],
        pageLength: 25,
        ajax: {
          url: table.getAttribute("data-kx-url"),
          type: "POST",
        },
        language: {},
        autoWidth: false,
        columns: JSON.parse(table.getAttribute("data-kx-columns")),
        order: JSON.parse(table.getAttribute("data-kx-order")),
        initComplete: function () {
          this.api()
            .columns()
            .every(function () {
              var that = this;
              $("input", this.footer()).on("keyup change clear", function () {
                if (that.search() !== this.value) {
                  that.search(this.value).draw();
                }
              });
              $("select", this.footer()).on("change clear", function () {
                if (that.search() !== this.value) {
                  if (this.value === "") {
                    that.search(this.value).draw();
                  } else {
                    that.search(this.value, true, false).draw();
                  }
                }
              });
            });
        },
        drawCallback: function () {
          // bootstrap tooltips
          $("[data-bs-toggle='tooltip']").each((i, el) => {
            bootstrap.Tooltip.getOrCreateInstance(el);
          });
        },
      };
      if (this.langDefinitions[this.lang]) {
        options.language.buttons = this.langDefinitions[this.lang].buttons;
        if (this.lang === "tr") {
          options.language.url = "/assets/libs/datatables/locale/tr.json";
        }
      }
      window.kxTables[tableKey] = $(table).DataTable(options);
    });
  }
}

(function (w) {
  // Initialize
  NProgress.start();

  $(document).pjax('a:not([target="_blank"]):not([data-direct])', "body");
  $(document).on("submit", 'form[data-kx-form="direct"]', function (event) {
    $.pjax.submit(event, "body");
  });

  $(document).on("pjax:send", function () {
    NProgress.start();
  });

  $(document).on("pjax:popstate pjax:end", function () {
    NProgress.done();
    w.kx.init(false, true);
  });

  // Initialize KalipsoXJS
  w.kx = new KalipsoXJS();
})(window);
