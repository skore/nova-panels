<template>
    <div>
        <slot>
            <h4 v-if="panel.showTitle" class="text-90 font-normal text-2xl mb-3">{{ panel.name }}</h4>
        </slot>

        <div class="relationship-tabs-panel card">
            <div class="tabs-wrap border-b-2 border-40 w-full">
                <div class="tabs flex flex-row overflow-x-auto">
                    <button
                        class="py-5 px-8 border-b-2 focus:outline-none tab"
                        :class="getTabClass(tab)"
                        v-for="(tab, key) in firstTabs"
                        :key="key"
                        @click="handleTabClick(tab)"
                    >
                        <tab-title :tab="tab" />
                    </button>

                    <select
                      v-if="Object.keys(lastTabs).length > 0"
                      @change="handleTabSelect($event)"
                      class="p-2 border-b-2 focus:outline-none tab cursor-pointer"
                      :class="getTabClass()"
                    >
                      <option v-if="!activeTab">{{ moreText }}</option>
                      <option v-for="(tab, key) in lastTabs" :key="key" :value="key">
                        <tab-title :tab="tab" />
                      </option>
                    </select>
                </div>
            </div>
            <div
                :class="[
                    (panel && panel.defaultSearch) ? 'default-search' : 'tab-content',
                    tab.slug,
                ]"
                :ref="getTabRefName(tab)"
                v-for="(tab, index) in tabs"
                v-show="tab.slug === activeTab"
                :label="tab.name"
                :key="'related-tabs-fields' + index"
            >
                <div
                    v-if="tab.init"
                    :class="getBodyClass(tab)"
                >
                    <component
                        v-for="(field, index) in tab.fields"
                        :class="{'remove-bottom-border': index === tab.fields.length - 1}"
                        :key="'tab-' + index"
                        :is="componentName(field)"
                        :resource-name="resourceName"
                        :resource-id="resourceId"
                        :resource="resource"
                        :field="field"
                        @actionExecuted="actionExecuted"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import BehavesAsPanel from 'laravel-nova/src/mixins/BehavesAsPanel';
import TabTitle from './TabTitle';
import { changeActiveTab } from '../../util/tab-updater';

export default {
    components: { TabTitle },

    mixins: [BehavesAsPanel],

    data() {
        return {
            tabs: null,
            activeTab: ''
        };
    },

    mounted() {
        const tabs = this.tabs = this.panel.fields.reduce((tabs, field) => {
            if (!(field.tabSlug in tabs)) {
                tabs[field.tabSlug] = {
                    name: field.tab,
                    slug: field.tabSlug,
                    init: false,
                    listable: field.listableTab,
                    fields: [],
                    properties: field.tabInfo,
                };
            }

            tabs[field.tabSlug].fields.push(field);

            return tabs;
        }, {});

        if (this.$route.query.tab !== undefined && tabs[this.$route.query.tab] !== undefined) {
            this.handleTabClick(tabs[this.$route.query.tab]);
        } else {
            this.handleTabClick(tabs[Object.keys(tabs)[0]], false);
        }
    },

    computed: {
      firstTabs() {
        if (!this.tabs) {
          return {};
        }

        return Object.fromEntries(
          Object.entries(this.tabs).slice(0, this.panel.tabsLimit || 5)
        );
      },

      lastTabs() {
        if (!this.tabs) {
          return {};
        }

        let tabs = Object.entries(this.tabs);

        return Object.fromEntries(tabs.slice(this.panel.tabsLimit || 5, tabs.length));
      }
    },
    
    methods: {
        /**
         * Handle the actionExecuted event and pass it up the chain.
         */
        actionExecuted() {
            this.$emit('actionExecuted');
        },

        handleTabSelect(event, updateUri = true) {
            const tab = this.tabs[event.target.value];
            const currentTab = this.$router.currentRoute.query;
            tab.init = true;
            this.activeTab = tab.slug;

            if (updateUri && (!currentTab || currentTab.tab !== tab.slug)) {
                changeActiveTab(this.$router, tab.slug);
            }
        },

        handleTabClick(tab, updateUri = true) {
            const currentTab = this.$router.currentRoute.query;
            tab.init = true;
            this.activeTab = tab.slug;

            if (updateUri && (!currentTab || currentTab.tab !== tab.slug)) {
                changeActiveTab(this.$router, tab.slug);
            }
        },

        componentName(field) {
            return field.prefixComponent
                ? 'detail-' + field.component
                : field.component;
        },

        getTabClass(tab = null) {
            const classes = [];

            if (!this.tabs) {
              return classes;
            }

            if (!tab) {
                const lastTabs = Object.entries(this.lastTabs);

                tab = lastTabs.filter(val => this.activeTab === val[1].slug)
                tab = tab[0] ? tab[0][1] : lastTabs[0][1];

                console.log(tab)
            }

            this.activeTab === tab.slug
                ? classes.push('text-grey-black font-bold border-primary')
                : classes.push('text-grey font-semibold border-40');
            
            return classes.concat(tab.properties.tabClass);
        },

        getBodyClass(tab) {
            const classes = [];
            if (!tab.listable) {
                classes.push('px-6 py-3');
            }
            return classes.concat(tab.properties.bodyClass);
        },

        getTabRefName(tab) {
            return `tab-${tab.slug}`;
        },
    }
};
</script>

<style lang="scss">
.relationship-tabs-panel {
    .has-search-bar {
    }
    .tabs::-webkit-scrollbar {
        height: 8px;
        border-radius: 4px;
    }
    .tabs::-webkit-scrollbar-thumb {
        background: #cacaca;
    }
    .tabs {
        white-space: nowrap;
        margin-bottom: -2px;
    }
    .card {
        box-shadow: none;
    }
    h1 {
        display: none;
    }
    .tab {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }
    .default-search > div > .relative > .flex {
        justify-content: flex-end;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        margin-top: 0.75rem;
        margin-bottom: 0.75rem;
        > .mb-6 {
            margin-bottom: 0;
        }
    }
    .default-search > div > .relative > .card > .flex {
        padding-top: 0;
    }
    .tab-content > div > .relative > .flex {
        justify-content: flex-end;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        position: absolute;
        top: 0;
        right: 0;
        transform: translateY(-100%);
        align-items: center;
        height: 62px;
        z-index: 1;
        > .w-full {
            width: auto;
            margin-left: 1.5rem;
        }
        .mb-6 {
            margin-bottom: 0;
        }
    }
}
</style>
