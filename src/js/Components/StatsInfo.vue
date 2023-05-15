<template>
  <div>
    <div style="display: flex; align-items: center; padding: 8px">
      <h3 style="padding: 0px 10px">
        <div style="min-width: 120px">
          <i
            style="color: blue; cursor: pointer"
            @click="$router.push('/')"
            class="el-icon-s-home"
          ></i>
          / All
        </div>
      </h3>

      <el-input @keyup.native.enter="find" v-model="searchText">
        <el-button
          slot="append"
          @click="find"
          icon="el-icon-search"
        ></el-button>
      </el-input>
    </div>
    <el-table v-loading="fetching" :data="attendees" style="width: 100%">
      <el-table-column label="Attendee ID" width="105">
        <template slot-scope="scope">
          <span style="cursor: pointer; color: blue" @click="view(scope.row)">{{
            scope.row.attendee_id
          }}</span>
        </template>
      </el-table-column>
      <el-table-column width="70">
        <template slot-scope="scope">
          <img
            :src="scope.row.gravatar"
            style="
              width: 40px;
              height: 40px;
              border-radius: 50%;
              margin-right: 10px;
            "
          />
        </template>
      </el-table-column>
      <el-table-column label="First Name">
        <template slot-scope="scope">
          <span>{{ scope.row.first_name }} {{ scope.row.last_name }}</span>
          <br />
          <span style="font-size: 12px; margin: 0px; color: #9c9b9b">
            {{ scope.row.email }}
          </span>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      background
      @size-change="pageSizeChange"
      @current-change="changePage"
      :current-page.sync="pagination.current_page"
      :page-size="pagination.per_page"
      layout="prev, pager"
      :total="pagination.total"
    >
      layout="prev, pager, next" :total="attendees.total">
    </el-pagination>

    <el-dialog
      title="Attendee"
      :visible.sync="viewAttendee"
      width="400px"
      center
    >
      <attendee-profile :attendee="selectedAttendee"></attendee-profile>
    </el-dialog>

    <!-- <el-dialog :visible-sync="viewAttendee">
      hello
      <attendee-profile :attendee="selectedAttendee"></attendee-profile>
    </el-dialog> -->
  </div>
</template>
<script>
import AttendeeProfile from './AttendeeProfile.vue'

export default {
  name: 'StatsInfo',
  components: {
    AttendeeProfile
  },
  data () {
    return {
      fetching: false,
      attendees: [],
      viewAttendee: false,
      selectedAttendee: {},
      searchText: '',
      pagination: {
        current_page: 1,
        per_page: 10,
        page_number: 1,
        total: 0
      }
    }
  },
  methods: {
    view (attendee) {
      this.selectedAttendee = attendee
      this.viewAttendee = true
    },
    find () {
      this.pagination.current_page = 1
      this.getInfo()
    },
    pageSizeChange (pageSize) {
      this.pagination.per_page = pageSize
      this.getInfo()
    },
    changePage (page) {
      this.pagination.current_page = page
      this.pagination.page_number = page
      this.getInfo()
    },
    getInfo () {
      this.fetching = true
      this.$get({
        action: 'scan_attendee_admin_ajax',
        route: 'get_attendees',
        pagination: this.pagination,
        search: this.searchText
      })
        .then(response => {
          this.attendees = response.data.attendees
          this.pagination.total = parseInt(response.data.total)

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
