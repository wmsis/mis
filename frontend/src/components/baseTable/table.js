import Vue from 'vue';
import {
    Table
} from 'iview';

Vue.component('Table', Table);

export default {
  props: {
    columns: {
      type: Array
    },
    height: {
      type: String|Number
    },
    highlightRow: {
      type: Boolean,
      default: false
    },
    data: {
      type: Array,
      default: () => ([])
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  render(h) {
    const { columns, data, height, highlightRow, loading } = this;
    return h('Table', {
      ref: 'table',
      props: {
        border: true,
        columns,
        size: 'small',
        height,
        highlightRow,
        data,
        loading,
        stripe: true,
      },
      scopedSlots: this.$parent.$scopedSlots,
      on: {
        'on-selection-change':  (rows) => {
          this.$emit('on-selection-change', rows);
        },
        'on-current-change': (row) => {
          this.$emit('on-current-change', row);
        },
        'on-row-click': (row) => {
          this.$emit('on-row-click', row);
        }
      }
    });
  },

  methods: {
    reload() {
      this.$refs.table.reload();
    },

    getSelectedRows() {
      return this.$refs.table.getSelectedRows();
    },

    clearCurrentRow() {
      return this.$refs.table.clearCurrentRow();
    }
  }
}