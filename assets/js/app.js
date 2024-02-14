NProgress.start();
class KalipsoXJS {
  // version
  version = "1.0.0";

  constructor() {
    this.init();
    return this;
  }

  init() {
    //event listeners
    document.addEventListener("click", (e) => {
      let el = e.target;

      if (
        e.target.nodeName.toUpperCase() === "BUTTON" ||
        e.target.nodeName.toUpperCase() === "A"
      ) {
        if (el.hasAttribute("data-kx-action")) {
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
        }
      }
    });

    document.addEventListener("submit", (e) => {
      let el = e.target;
      if (el.hasAttribute("data-kx-form")) {
        e.preventDefault();
        const form = el;
        const url = form.getAttribute("action");
        const method = form.getAttribute("method");
        const data = new FormData(form);
        const headers = new Headers();
        headers.append("X-Requested-With", "XMLHttpRequest");
        headers.append("Accept", "application/json");
        fetch(url, {
          method: method,
          body: data,
          headers: headers,
        })
          .then((response) => response.json())
          .then((data) => {
            console.log(data);
          })
          .catch((error) => {
            this.notify("An error occurred!", "error");
            console.error("Error:", error);
          });
      }
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

    setTimeout(() => {
      NProgress.done();
    }, 1000);
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

  notify(message, type = "info") {
    Toastify({
      text: message,
      close: true,
      duration: 300000,
      gravity: "top",
      position: "right",
      stopOnFocus: true,
      className: "toastn " + type,
      escapeMarkup: false,
    }).showToast();
  }
}

window.kx = new KalipsoXJS();
