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

  constructor() {
    this.init();
    return this;
  }

  init(firstLoad = true) {
    // Scroll to top
    $("html, body").animate({ scrollTop: 0 }, "slow");

    this.dateTime = luxon.DateTime;

    console.log("KalipsoXJS v" + this.version + " initialized!");

    this.lang = document.documentElement.lang;

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
          el.querySelector("i.ti").classList.value = el
            .querySelector("i.ti")
            .classList.value.replace("ti-", "ti--");
          el.querySelector("i.ti").classList.add("ti-question-mark");
          setTimeout(() => {
            el.setAttribute("data-kx-again", "");
            el.querySelector("i.ti").classList.value = el
              .querySelector("i.ti")
              .classList.value.replace("ti--", "ti-");
            el.querySelector("i.ti").classList.remove("ti-question-mark");
          }, 3000);
          return;
        }
      }

      switch (action) {
        case "toggle_theme":
          const newTheme =
            document.body.getAttribute("data-bs-theme") === "light"
              ? "dark"
              : "light";
          this.changeColorScheme(newTheme);
          if (el.querySelector("i.ti")) {
            el.querySelector("i.ti").setAttribute("class", "ti");
            el.querySelector("i.ti").classList.add(
              newTheme === "dark" ? "ti-sun" : "ti-moon"
            );
          }
          break;
        case "show_password":
          const parent = this.findParent(el, ".input-group");
          if (parent) {
            const input = parent.querySelector("input");
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
        default:
          // direct send request
          const data = el.getAttribute("data-kx-body")
            ? JSON.parse(el.getAttribute("data-kx-body"))
            : {};
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

    if ($("body").hasClass("dashboard")) {
      this.startHeartbeat();
    }

    // Form submit
    $('form[data-kx-form]:not([data-kx-form="direct"])').each((i, form) => {
      $(form).off("submit");
      $(form).on("submit", async (event) => {
        event.preventDefault();
        await this.sendForm(event);
      });
    });

    // color scheme
    if (localStorage.getItem("kx_theme")) {
      this.changeColorScheme(localStorage.getItem("kx_theme"));
    } else {
      if (
        window.matchMedia &&
        window.matchMedia("(prefers-color-scheme: dark)").matches
      ) {
        this.changeColorScheme("dark");
      } else {
        this.changeColorScheme("light");
      }
    }

    if (typeof window.frontInit === "function") {
      window.frontInit();
    }

    // bootstrap tooltips
    var tooltipTriggerList = [].slice.call(
      document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    if (firstLoad) {
      setTimeout(() => {
        NProgress.done();
      }, 250);
    }
  }

  async sendForm(event) {
    // find submit button
    const submitButton =
      event.target.querySelector('button[type="submit"]') ||
      document.querySelector(
        '[form="' + event.target.getAttribute("id") + '"]'
      );
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

  async sendRequest(
    url = null,
    method = "POST",
    data = {},
    progressBar = true
  ) {
    if (progressBar) {
      NProgress.inc();
    }
    url = url ?? window.location.href;

    method = method ?? "POST";
    method = method.toUpperCase();

    data = typeof data === "string" ? JSON.parse(data) : data;
    data = typeof data === "object" ? data : {};

    let headers = {};

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

      const resursiveFormData = (
        obj,
        formData = new FormData(),
        parentKey = null
      ) => {
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
        if (typeof error.message === "string") {
          message = error.message;
        } else {
          return error;
        }
        this.notify(
          "A problem occurred!" + (message === "" ? "" : "(" + message + ")"),
          "error"
        );
      });
    if (progressBar) {
      NProgress.done();
    }
    return ret;
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
                  if (currentHtml !== value) {
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
              if (
                typeof data.redirect.direct !== "undefined" &&
                data.redirect.direct === true
              ) {
                window.location.reload();
              } else {
                $.pjax.reload();
              }
            } else {
              if (
                typeof data.redirect.direct !== "undefined" &&
                data.redirect.direct === true
              ) {
                window.location.href = data.redirect.url;
              } else {
                $.pjax({ url: data.redirect.url, container: "body" });
              }
            }
          }, data.redirect.time || 0);
        }
      }

      // form reset
      if (typeof data.form_reset !== "undefined" && data.form_reset === true) {
        $(form).trigger("reset");
      }

      // form validation
      if (
        typeof data.heart_beat_stop !== "undefined" &&
        data.heart_beat_stop === true
      ) {
        clearInterval(window.kxHeartbeat);
      }

      // heart beat direct
      if (
        typeof data.heart_beat_direct !== "undefined" &&
        data.heart_beat_direct
      ) {
        this.heartBeat();
      }
    }
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
      $('[data-kx-action="toggle_theme"] i.ti').addClass(
        color === "dark" ? "ti-sun" : "ti-moon"
      );
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
    const response = await this.sendRequest(
      "/auth/heartbeat",
      "POST",
      {},
      false
    );
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
      window.kxHeartbeat = setInterval(heartbeat, 5000);
    }
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
    w.kx.init(false);
  });

  // Initialize KalipsoXJS
  w.kx = new KalipsoXJS();
})(window);
