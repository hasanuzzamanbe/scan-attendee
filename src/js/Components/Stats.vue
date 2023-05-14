<template>
  <div>
    <div style="display: flex; justify-content: center; align-items: center">
      <div>
        <h1>Stats</h1>
        <hr />
        <div v-if="fetching">Getting info....</div>

        <!-- table to show info -->
        <div class="align_center">
          <table id="attendee_info">
            <tr>
              <th>Category</th>
              <th>Quantity</th>
            </tr>
            <tr>
              <td>Checked-in</td>
              <td>{{ info.checked_in }}</td>
            </tr>
            <tr>
              <td>Not checked-in</td>
              <td>{{ info.total - info.checked_in }}</td>
            </tr>
            <tr>
              <td>Breakfast</td>
              <td>{{ info.has_breakfast }}</td>
            </tr>
            <tr>
              <td>Lunch</td>
              <td>{{ info.has_lunch }}</td>
            </tr>
            <tr>
              <td>Total</td>
              <td>{{ info.total }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name: 'Supports',
  data () {
    return {
      fetching: false,
      info: {
      }
    }
  },
  methods: {
    getInfo () {
      this.fetching = true
      this.$get({
        action: 'scan_attendee_admin_ajax',
        route: 'get_info'
      })
        .then(response => {
          this.info = response.data.info
          this.fetching = false
        })
        .always(() => {
          this.fetching = false
        })
        .fail(error => {
          this.$message.error({
            message: error.responseJSON.data,
            offset: 100
          })
        })
    }
  },
  mounted () {
    this.getInfo()
  }
}
</script>
<style>
#attendee_info {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#attendee_info td,
#attendee_info th {
  border: 1px solid #ddd;
  padding: 8px;
}

#attendee_info tr:nth-child(even) {
  background-color: #f2f2f2;
}

#attendee_info tr:hover {
  background-color: #ddd;
}

#attendee_info th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #04aa6d;
  color: white;
}
</style>
