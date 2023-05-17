<template>
  <div>
    <!-- attendee profile -->
    <div class="scan_attendee_profile_container align_center">
      <div v-if="attendee.attendee_id" class="scan_attendee_profile">
        <h4 class="name">
          {{ getFullName() }}
          <span style="color: green; margin-left: 4px"
            >{{ attendee.attendee_id }}</span
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
        <!-- <p class="t-shirt-size">T-shirt: {{ attendee.t_shirt_size }}</p> -->
        <div class="action" style="margin-bottom: 23px">
          <div>
            <input
              type="checkbox"
              @change="update"
              id="has_giftbox"
              v-model="attendee.has_giftbox"
              true-value="yes"
              false-value="no"
            />
            <label for="has_giftbox">GiftBox</label>
          </div>

          <div>
            <input
              type="checkbox"
              @change="update"
              id="has_tshirt"
              v-model="attendee.has_tshirt"
              true-value="yes"
              false-value="no"
            />
            <label for="has_tshirt">T-shirt</label>
          </div>

          <div>
            <input
              type="checkbox"
              @change="update"
              id="has_swag"
              v-model="attendee.has_swag"
              true-value="yes"
              false-value="no"
            />
            <label for="has_swag">Swag</label>
          </div>
        </div>


        <p v-if="attendee.has_giftbox !== 'yes' 
          || attendee.has_tshirt !== 'yes' 
          || attendee.has_swag !== 'yes'" 
          style="margin-top: 10px;
            background: #a9ffa9;
            padding: 10px;">
            Please distribute 
            <span v-if="attendee.has_giftbox !== 'yes'">GiftBox</span>
            <span v-if="attendee.has_tshirt !== 'yes'">,T-Shirt</span>
            <span v-if="attendee.has_swag !== 'yes'">,Swag</span> to the attendee.
        </p>
        <p v-else>
          <span style="font-weight: bold;padding: 10px;background: #fed50b;">Swag distributed at:
            {{ attendee.updated_at }}
          </span>
        </p>

        <div>
          <label for="">Name (optional)</label>
          <el-input
            placeholder="Name(optional)"
            class="comment"
            v-model="attendee.first_name"
          >
            <el-button
              slot="append"
              icon="el-icon-check"
              @click="addName"
            ></el-button>
          </el-input>
        </div>

        <div>
          <label for="">Email (optional)</label>
          <el-input
            placeholder="Email (optional)"
            class="comment"
            v-model="attendee.email"
          >
            <el-button
              slot="append"
              icon="el-icon-check"
              @click="addEmail"
            ></el-button>
          </el-input>
        </div>

        <span
          v-if="attendee.update_by_agent"
          style="color: #ababab; font-size: 12px; font-style: italic"
        >
          Distributed by: {{ attendee.update_by_agent }}
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
    addEmail () {
      this.$post({
        action: 'scan_attendee_admin_ajax',
        route: 'add_email',
        attendee_id: this.attendee?.attendee_id,
        email: this.attendee?.email
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
    addName () {
      this.$post({
        action: 'scan_attendee_admin_ajax',
        route: 'add_name',
        attendee_id: this.attendee?.attendee_id,
        first_name: this.attendee?.first_name
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
