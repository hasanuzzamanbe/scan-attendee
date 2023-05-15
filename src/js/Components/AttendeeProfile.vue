<template>
  <div>
    <!-- attendee profile -->
    <div class="scan_attendee_profile_container align_center">
      <div v-if="attendee.attendee_id" class="scan_attendee_profile">
        <h4 class="name">
          {{ getFullName() }}
          <span style="color: green; margin-left: 4px"
            >({{ attendee.attendee_id }})</span
          >
          <i
            style="cursor: pointer; float: right"
            class="el-icon-refresh"
            @click="$emit('refresh')"
          ></i>
        </h4>
        <div class="profile_image_section">
          <div>
            <p class="email">{{ attendee.email }}</p>
            <p class="ticket-type">{{ attendee.ticket_type }}</p>
          </div>
          <div>
            <img
              v-if="attendee.gravatar"
              :src="attendee.gravatar"
              alt="profile"
            />
          </div>
        </div>
        <p class="t-shirt-size">T-shirt: {{ attendee.t_shirt_size }}</p>
        <div class="action" style="margin-bottom: 23px">
          <div>
            <input
              type="checkbox"
              @change="update"
              id="checkin"
              v-model="attendee.checkin"
              true-value="yes"
              false-value="no"
            />
            <label for="checkin">Check-in</label>
          </div>

          <div>
            <input
              type="checkbox"
              @change="update"
              id="breakfast"
              v-model="attendee.breakfast"
              true-value="yes"
              false-value="no"
            />
            <label for="breakfast">Breakfast</label>
          </div>

          <div>
            <input
              type="checkbox"
              @change="update"
              id="lunch"
              v-model="attendee.lunch"
              true-value="yes"
              false-value="no"
            />
            <label for="lunch">Lunch</label>
          </div>
        </div>

        <el-input
          placeholder="Note(optional)"
          class="comment"
          v-model="attendee.comment"
        >
          <el-button
            slot="append"
            icon="el-icon-check"
            @click="addNote"
          ></el-button>
        </el-input>
        <span
          v-if="attendee.update_by_agent"
          style="color: #ababab; font-size: 12px; font-style: italic"
        >
          Last update by: {{ attendee.update_by_agent }}
        </span>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  props: {
    attendee: {
      type: Object,
      default: () => {}
    }
  },
  methods: {
    addNote () {
      this.$post({
        action: 'scan_attendee_admin_ajax',
        route: 'add_note',
        attendee_id: this.attendee?.attendee_id,
        note: this.attendee?.comment
      })
        .then(response => {
          this.$message.success({
            message: response.data.message,
            offset: 50
          })
        })
        .always(() => {
          // this.fetching = false;
        })
        .fail(error => {
          console.log(error)
          this.$message.error({
            message: error.responseJSON.data,
            offset: 100
          })
        })
    },
    getFullName () {
      return this.attendee?.first_name + ' ' + this.attendee?.last_name
    },
    update (e) {
      this.$post({
        action: 'scan_attendee_admin_ajax',
        route: 'update_attendee',
        attendee_id: this.attendee?.attendee_id,
        type: e.target.id,
        value: e.target.checked ? 'yes' : 'no'
      })
        .then(response => {
          this.$message.success({
            message: response.data.message,
            offset: 50
          })
          this.showManualInput = false
        })
        .always(() => {
          // this.fetching = false;
        })
        .fail(error => {
          console.log(error)
          this.$message.error({
            message: error.responseJSON.data,
            offset: 100
          })
        })
    }
  }
}
</script>
