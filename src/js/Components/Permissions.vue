<template>
  <div class="all_payforms_wrapper payform_section scan_attendee_min_width">
    <el-row v-if="roles.length" style="padding: 23px">
      <div class="scan_attendee_ninja_header">
        <h2>Scan Attendee Admin Permission</h2>
      </div>
      <div v-loading="loading" class="scan_attendee_ninja_content">
        <div class="scan_attendee_ninja_block">
          <p>
            By default, only Administrator have access to manage the forms. By selecting additional roles bellow, you can give access to manage your forms to other user roles.
          </p>
        </div>
        <hr />
        <template>
          <div class="scan_attendee-form-group">
            <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">
              Check All
            </el-checkbox>
          </div>

          <div class="scan_attendee-form-group">
            <el-checkbox-group class="scan_attendee_role_group" v-model="capability" @change="handleCheckedCapabilitiesChange">
              <el-checkbox v-for="role in roles" :label="role.key" :key="role.key">
                {{ role.name }}
              </el-checkbox>
            </el-checkbox-group>
          </div>
        </template>
        <div class="scan_attendee_settings_section">
          <div class="action_right" style="padding: 16px">
            <el-button @click="store()" type="primary" size="medium" class="scan_attendee_new_ui_button">Save Settings</el-button>
          </div>
        </div>
      </div>
    </el-row>
  </div>
</template>

<script>

import CheckboxGroup from './CheckboxGroup.vue';
export default {
  name: "Permissions",
  components: {
    CheckboxGroup
  },
  data() {
    return {
      loading: false,
      roles: [],
      checkAll: false,
      capability: ["administrator"],
      isIndeterminate: false,
      paymatticUserDashboardStatus: false,
      paymatticCustomUser: [],
      checkboxGroup: null
    };
  },
  methods: {
    permissionUpdate() {
      this.loading = true;
      this.$post("forms/settings/update-paymattic-user-permission", {
        paymatticUserPermissions: this.paymatticCustomUser,
      })
        .then((res) => {
          this.loading = false;
          this.$notify.success({
            title: 'Success!',
            message: res.message,
            offset: 30,
            position: 'bottom-right'
          });
        })
        .fail((e) => {
          this.loading = false;
          this.$notify.error({
            title: 'Error!',
            message: e.responseJSON.message,
            offset: 30,
            position: 'bottom-right'
          });
        })
        .always(() => {
          this.loading = false;
        });
    },

    get() {
      this.loading = true;
      // GlobalSettingsController@roles
      this.$get({
            action: 'scan_attendee_admin_ajax',
            route: 'get_permissions'
        })
        .then((response) => {
          let capability = response.data.roles.capability;
          if (!capability || typeof capability != "object") {
            capability = ["administrator"];
          }
          this.capability = capability;
          this.roles = response.data.roles.roles;
          this.handleCheckedCapabilitiesChange(this.capability);
        })
        .fail((e) => { })
        .always(() => {
          this.loading = false;
        });
    },
    store() {
      if (!this.roles.length) {
        return;
      }
      // GlobalSettingsController@setRoles
      this.loading = true;
      this.$post({
        action: 'scan_attendee_admin_ajax',
        route: 'update_permissions',
        capability: this.capability,
      })
        .then((res) => {
          this.loading = false;
          this.$notify.success({
            title: 'Success!',
            message: res.message,
            offset: 30,
            position: 'bottom-right'
          });
        })
        .fail((e) => {
          this.loading = false;
          this.$notify.error({
            title: 'Error!',
            message: e.responseJSON.message,
            offset: 30,
            position: 'bottom-right'
          });
        })
        .always(() => {
          this.loading = false;
        });
    },
    handleCheckAllChange(val) {
      this.capability = val ? this.roles.map((item) => item.key) : [];
      this.isIndeterminate = false;
    },
    handleCheckedCapabilitiesChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.roles.length;
      this.isIndeterminate =
        checkedCount > 0 && checkedCount < this.roles.length;
    },
  },
  mounted() {
    this.get();
  },
};
</script>

<style scoped>
.scan_attendee-form-group {
  margin-bottom: 20px;
}

.scan_attendee_role_group label {
  margin-top: 20px;
}
</style>
