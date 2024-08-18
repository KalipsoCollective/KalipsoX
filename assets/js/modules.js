const { createApp } = Vue;

/* Language Editor */
createApp({
  data() {
    return {
      bulkData: window.languageList,
      languageTerms: {},
    };
  },

  created() {
    this.languageTerms = {};
    for (const lang of this.languages) {
      const flattened = this.multiDimensionToSingleDimension(this.bulkData[lang]);
      for (const key in flattened) {
        if (!this.languageTerms[key]) {
          this.languageTerms[key] = {};
        }
        this.languageTerms[key][lang] = flattened[key];
      }
    }
  },

  methods: {
    addTerm() {
      const newKey = prompt("Enter the new term key:");
      if (newKey) {
        for (const lang in this.languageList) {
          this.$set(this.languageList[lang], newKey, "");
        }
      }
    },
    deleteTerm(key) {
      delete this.languageTerms[key];
      /*
      for (const lang in this.languageList) {
        delete this.languageList[lang][key];
      }*/
    },
    updateTerm(oldKey, newKey) {
      let revisedTerms = {};
      for (const key in this.languageTerms) {
        if (key === oldKey) {
          revisedTerms[newKey] = this.languageTerms[key];
        } else {
          revisedTerms[key] = this.languageTerms[key];
        }
      }
      this.languageTerms = { ...revisedTerms };
    },
    addLanguage() {
      this.languageList["test"] = {};
    },
    multiDimensionToSingleDimension(data, parentKey = "") {
      let result = {};
      for (let key in data) {
        const fullKey = parentKey ? `${parentKey}.${key}` : key;
        if (typeof data[key] === "object") {
          Object.assign(result, this.multiDimensionToSingleDimension(data[key], fullKey));
        } else {
          result[fullKey] = data[key];
        }
      }
      return result;
    },
  },
  computed: {
    languages: function () {
      return Object.keys(this.bulkData);
    },
  },
}).mount("#languageEditor");
