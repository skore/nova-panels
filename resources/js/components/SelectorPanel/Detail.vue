<template>
  <div class="relationship-selector" v-if="field.options.length">
    <div class="mb-3" v-if="!field.withSelect">
      <select
        v-model="activeTab"
        class="form-select form-selector shadow-none border-0 text-90 font-normal text-2xl bg-transparent py-2 pl-0 focus:border-0 focus:outline-none focus:shadow-none"
        :class="{'only-one-option': field.options.length == 1}"
      >
        <option
          v-for="(tab, key) in options"
          :key="key"
          :value="tab.name"
          class="text-base"
        >&nbsp;{{ tab.name }}</option>
      </select>
    </div>
    <div
      class="mb-3 flex items-center"
      v-if="field.withSelect"
    >
      <h1 class="text-90 font-normal text-2xl flex-no-shrink">{{ activeTab }}</h1>
      <div class="ml-3 w-full flex items-center"></div>
      <select v-model="activeTab" class="form-control form-select">
        <option v-for="(tab, key) in options" :key="key" :value="tab.name">{{ tab.name }}</option>
      </select>
    </div>
    <div class="relationship-selector-content">
      <div
        v-for="(tab, index) in panel.options"
        
        :label="tab.name || tab.field.resourceName.toLocaleUpperCase()"
        :key="'related-options-fields' + index"
        :name="tab.field.resourceName"
      >
        <component
          v-if="tab.field.component == 'belongs-to-field'"
          :is="'panel'"
          :resource-name="resourceName"
          :resource-id="resourceId"
          :resource="resource"
          :panel="{ showToolbar: false, component:'panel', fields: [tab.field], name: tab.field.panel }"
          @actionExecuted="actionExecuted"
        />
        <component
          v-else
          :is="'detail-' + tab.field.component"
          :resource-name="resourceName"
          :resource-id="resourceId"
          :resource="resource"
          :field="tab.field"
          @actionExecuted="actionExecuted"
        />
      </div>
    </div>
  </div>
</template>

<script>
import BehavesAsPanel from 'laravel-nova/src/mixins/BehavesAsPanel';

export default {
  mixins: [BehavesAsPanel],

  data() {
    return {
      activeTab: ""
    };
  },
  
  mounted() {
    this.activeTab = this.options[0].name;
  },
  
  methods: {
    /**
     * Handle the actionExecuted event and pass it up the chain.
     */
    actionExecuted() {
      this.$emit("actionExecuted");
    },
    
    handleTabClick(tab, event) {}
  }
};
</script>

<style lang="scss">
.relationship-selector {
  .relationship-selector-content {
    min-height: 355px;
  }
  .form-select.only-one-option {
    background-image: none;
    pointer-events: none;
  }
  .form-selector {
    text-indent: -5px;
  }
  .form-select:focus {
    outline: none;
    -webkit-box-shadow: none;
    box-shadow: none;
  }
}

.relationship-selector-content {
  h1 {
    display: none;
  }
  h4.text-2xl.mb-3 {
    display: none;
  }
}
</style>