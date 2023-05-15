<template>
  <div>
    <div class="scan-action-header align_center">
        <div class="scan-action-left">
            Please scan the QR code on the attendee's badge
        </div>
        <div class="scan-action-right">
            <el-button v-if="isActive" size="small" type="danger" id="stop-button" @click="stop">Stop</el-button>
        </div>
    </div>
    <hr />
    <div v-show="!isActive" style="display: flex;
        align-items: center;
        justify-content: center; margin:10px 0px 10px 0px;">

        <el-button class="scan_camera_button" icon="el-icon-camera-solid" size="small" type="primary" id="start-button" 
        style="" @click="start"></el-button>

        <div class="scan_manual_wrap scan_manual_button">
            <el-button class="scan_stats_button" size="small" type="info" id="start-button" 
            style="" @click="showStats = true" icon="el-icon-info"></el-button>

            <el-button class="scan_stats_button" size="small" type="info" id="start-button" 
            style="" @click="$router.push('attendees')" icon="el-icon-s-grid"></el-button>

            <el-button class="scan_manual_button" icon="el-icon-search" size="small" type="info" id="start-button" 
            style="" @click="findManually"></el-button>      
        </div>
        

    </div>
    <div style="text-align: center;">
        <div>
            <!-- <b>Detected QR code: </b> -->
            <div class="scan-result">
                <p id="cam-qr-result" style="color: inherit">No QR scanned yet, 
                    <span @click="findManually">Input Manually?</span>
                </p>
            </div>
        </div>

    </div>

    <div v-if="fetching" 
        class="align_center" 
        style="margin-top: 23px; color: green;">
        Searching for attendee...
    </div>

    <attendee-profile 
     @refresh="refresh" :attendee="attendee"></attendee-profile>

    <br />
    <div id="video-container">
      <video id="qr-video"></video>
      <div class="scan-region-highlight"></div>
    </div>

    <el-dialog
        v-loading="fetching"
        loading-icon="el-icon-loading"
        loading-text="Feching"
        title="Attendee Id"
        :visible.sync="showManualInput"
        width="400px"
        center>
        <div style="display: flex;
            justify-content: center;">
            <input @keyup.enter="getAttendee" clearable style="min-height: 45px;width: 300px;" autofocus placeholder="Enter Id" type="text" v-model="manualInput">
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="showManualInput = false">Cancel</el-button>
            <el-button :disabled="fetching" type="primary" @click="getAttendee">Search</el-button>
        </span>
        </el-dialog>

        <el-dialog @open="getInfo" width="300px" :visible.sync="showStats">
            <Stat :info="info"></Stat>
        </el-dialog>
  </div>
</template>
<script>
import QrScanner from 'qr-scanner'
import 'qr-scanner/qr-scanner-worker.min.js'
import Stat from './Stats.vue';
import AttendeeProfile from './AttendeeProfile.vue';
export default {
  name: 'Dashboard',
  data () {
    return {
      vueJs: 'https://vuejs.org/',
      scanner: null,
      isActive: false,
      showManualInput: false,
      manualInput: '',
      fetching: false,
      showStats: false,
      info: {},
      attendee: {
      },
      scanId: ''
    }
  },
  components: {
    Stat,
    AttendeeProfile
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
    },
    findManually() {
        this.showManualInput = true;
        document.getElementById('cam-qr-result').textContent = 'No QR scanned yet!'
    },
    refresh(){
        this.fetch(this.attendee?.attendee_id);
    },
    init () {
      const video = document.getElementById('qr-video')
      const camQrResult = document.getElementById('cam-qr-result')

      this.scanner = new QrScanner(
        video,
        result => this.setResult(camQrResult, result),
        {
          onDecodeError: error => {
            console.log(error, 'Error')
            camQrResult.textContent = error
            camQrResult.style.color = 'inherit'
          },
          highlightScanRegion: true,
          highlightCodeOutline: true
        }
      )
      window.scanner = this.scanner
    },
    update(e) {
        this.$post({
                action: 'scan_attendee_admin_ajax',
                route: 'update_attendee',
                attendee_id: this.attendee?.attendee_id,
                type: e.target.id,
                value: e.target.checked ? 'yes' : 'no'
            }).then(response => {
                this.$message.success({
                    message: response.data.message,
                    offset: 50
                })
                this.showManualInput = false;
            }).always(() => {
                // this.fetching = false;
            }).fail(error => {
                console.log(error)
                this.$message.error({
                    message: error.responseJSON.data,
                    offset: 100
                })
            });
    },
    fetch(attendeeId) {
        this.fetching = true;
        this.$get({
                action: 'scan_attendee_admin_ajax',
                route: 'get_attendee',
                attendee_id: attendeeId,
            }).then(response => {
                this.attendee = response.data.attendee
                this.showManualInput = false;
            }).always(() => {
                this.fetching = false;
                this.$message.success({
                    message: 'Attendee fetched',
                    offset: 50,
                    duration: 1000
                })
            }).fail(error => {
                this.fetching = false;
                console.log(error)
                this.$message.error({
                    message: error.responseJSON.data,
                    offset: 100
                })
            });
    },
    getAttendee() {
        this.fetch(this.manualInput);
    },
    start () {
        this.scanner.start();
        this.attendee = {};
        this.isActive = true;
    },
    stop () {
      this.scanner.stop();
      this.isActive = false;
    },
    setResult (label, result) {
        this.fetch(result.data);
        label.textContent = result.data
        if (result !== null) {
            this.stop()
        }
        label.style.color = 'teal'
        clearTimeout(label.highlightTimeout)
        label.highlightTimeout = setTimeout(
            () => (label.style.color = 'inherit'),
            100
        )
    }
  },
  mounted () {
    this.init()
  }
}
</script>
<style>
.scan-result {
    background: #fff;
    padding: 3px 10px;
    box-shadow: inset 0px 6px 6px 0px #ccc;
    text-align: center;
}
.scan-action-left {
    color: #989393;
    padding-top: 4px;
    font-size: 12px;
}
div#scan-attendee_app {
    /* max-width: 400px; */
    max-width: 600px;
    margin: 5px auto;
    border: 1px solid #ccc;
    height: 95vh;
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 14px;
    background: #f7f2ec;
}
.scan-action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 26px;
}
.scan_attendee_profile {
    width: 400px;
    /* border: 1px solid #dfdfdf; */
    padding: 21px;
    margin-top: 12px;
    /* box-shadow: 1px 0px 6px 0px #ccc; */
}

.scan_attendee_profile div.action {
    display: flex;
    justify-content: space-between;
}
.scan_attendee_profile h4.name {
    font-size: 26px;
    margin: 0;
    line-height: 30px;
}
.profile_image_section {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 3px;
}
.profile_image_section img {
    border-radius: 50%;
}
.align_center {
    display: flex;
    justify-content: center;
    align-items: center;
}
.scan_camera_button {
    z-index: 999;
    position: fixed;
    bottom: 10px;
    width: 84px;
    font-size: 36px !important;
    height: 84px;
    border-radius: 50% !important;
    right: 5px;
    box-shadow: -4px 2px 6px 3px #ccc;
    border: 2px solid white !important;
}
.scan_manual_wrap {
    z-index: 999;
    position: fixed;
    bottom: 30px;
    font-size: 14px !important;
    /* border: 2px solid white !important; */
}
</style>
