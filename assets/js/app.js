class KalipsoXJS {
  // version
  version = "1.0.0";

  constructor() {
    this.init();
    return this;
  }

  init(firstLoad = true) {
    // Scroll to top
    $("html, body").animate({ scrollTop: 0 }, "slow");

    // Event listeners
    $(document).off("click", "[data-kx-action]");
    $(document).on("click", "[data-kx-action]", (e) => {
      const el = e.target;
      const action = el.getAttribute("data-kx-action");
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
        default:
          break;
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
    this.pullResponse(response);
  }

  async sendRequest(url = null, method = "POST", data = {}) {
    NProgress.inc();
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
    NProgress.done();
    return ret;
  }

  pullResponse(data) {
    if (data && typeof data === "object") {
      if (typeof data.notify !== "undefined" && data.notify.length > 0) {
        data.notify.forEach((alert) => {
          this.notify(alert.message, alert.type);
        });
      }

      if (typeof data.dom !== "undefined" && Object.keys(data.dom).length > 0) {
        for (const [selector, manipulationData] of Object.entries(data.dom)) {
          if ($(selector)) {
            for (const [key, value] of Object.entries(manipulationData)) {
              switch (key) {
                case "html":
                  $(selector).html(value);
                  break;
                case "text":
                  $(selector).text(value);
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
                  $(selector).addClass(value);
                  break;
                case "removeClass":
                  $(selector).removeClass(value);
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

    if (document.querySelector('[data-kx-action="toggle_theme"] i.ti')) {
      document
        .querySelector('[data-kx-action="toggle_theme"] i')
        .setAttribute("class", "ti");
      document
        .querySelector('[data-kx-action="toggle_theme"] i')
        .classList.add(color === "dark" ? "ti-sun" : "ti-moon");
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
}

(function (w) {
  // Initialize
  NProgress.start();

  $(document).pjax('a:not([target="_blank"])', "body");
  $(document).on("submit", 'form[data-kx-form="direct"]', function (event) {
    $.pjax.submit(event, "body");
  });

  $(document).on("pjax:send", function () {
    NProgress.start();
  });

  $(document).on("pjax:popstate pjax:complete", function () {
    NProgress.done();
    w.kx.init(false);
  });

  // Initialize KalipsoXJS
  w.kx = new KalipsoXJS();
})(window);
