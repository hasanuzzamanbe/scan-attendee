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
        justify-content: center; margin:32px 0px 32px 0px;">
        <el-button  size="small" type="primary" id="start-button" 
        style="" @click="start">Scan by camera</el-button>
        <el-button  size="small" type="info" id="start-button" 
        style="" @click="findManually">Input Manually?</el-button>
    </div>
    <div class="align_center">
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

    <!-- attendee profile -->
    <div class="scan_attendee_profile_container align_center">
        <div v-if="attendee.attendee_id" class="scan_attendee_profile">
            <h4 class="name">{{ getFullName() }}
                <span style="color: green;margin-left:4px;">({{ attendee.attendee_id }})</span>
                <i style="cursor:pointer; float: right;" class="el-icon-refresh" @click="refresh"></i>
            </h4>
            <div class="profile_image_section">
                <div>
                    <p class="email">{{ attendee.email }}</p>
                    <p class="ticket-type">{{ attendee.ticket_type }}</p>        
                </div>
                <div>
                    <img v-if="attendee.gravatar" :src="attendee.gravatar" alt="profile">
                </div>

            </div>
            <p class="t-shirt-size">T-shirt: {{ attendee.t_shirt_size }}</p>
            <div class="action" style="margin-bottom: 23px;">
                <div>
                    <input type="checkbox" @change="update" id="checkin" v-model="attendee.checkin" true-value="yes" false-value="no"/>
                    <label for="checkin">Check-in</label> 
                </div>

                <div>
                    <input type="checkbox" @change="update" id="breakfast" v-model="attendee.breakfast" true-value="yes" false-value="no"/>
                    <label for="breakfast">Breakfast</label>     
                </div>

                <div>
                    <input type="checkbox" @change="update" id="lunch" v-model="attendee.lunch" true-value="yes" false-value="no"/>
                    <label for="lunch">Lunch</label> 
                </div>

            </div>

            <el-input placeholder="Note(optional)" class="comment" v-model="attendee.comment">
                <el-button slot="append" icon="el-icon-check" @click="addNote"></el-button>
            </el-input>
            <span v-if="attendee.update_by_agent" style="color: #ababab;
                font-size: 12px;
                font-style: italic;">
                Last update by: {{ attendee.update_by_agent }}
            </span>
        </div> 
    </div>


    <br />
    <div id="video-container">
      <video id="qr-video"></video>
      <div class="scan-region-highlight"></div>
    </div>

    <el-dialog
        v-loading="!fetching"
        loading-icon="el-icon-loading"
        loading-text="Feching"
        title="Attendee Id"
        :visible.sync="showManualInput"
        width="400px"
        center>
        <div style="display: flex;
            justify-content: center;">
            <input style="min-height: 45px;width: 300px;" autofocus placeholder="Enter Id" type="text" v-model="manualInput">
        </div>
        <span slot="footer" class="dialog-footer">
            <el-button @click="showManualInput = false">Cancel</el-button>
            <el-button :disabled="fetching" type="primary" @click="getAttendee">Search</el-button>
        </span>
        </el-dialog>
  </div>
</template>
<script>
import QrScanner from 'qr-scanner'
import 'qr-scanner/qr-scanner-worker.min.js'
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
      attendee: {
      },
      scanId: ''
    }
  },
  methods: {
    addNote() {
        this.$post({
                action: 'scan_attendee_admin_ajax',
                route: 'add_note',
                attendee_id: this.attendee?.attendee_id,
                note: this.attendee?.comment
            }).then(response => {
                this.$message.success({
                    message: response.data.message,
                    offset: 50
                })
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
    findManually() {
        this.showManualInput = true;
        document.getElementById('cam-qr-result').textContent = 'No QR scanned yet!'
    },
    refresh(){
        this.fetch(this.attendee?.attendee_id);
    },
    getFullName() {
        return this.attendee?.first_name + ' ' + this.attendee?.last_name
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
        console.log('fetching')
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
  background: white;
  padding: 3px 10px;
  width: 424px;
  box-shadow: 0px 0px 4px #ccc;
  text-align: center;
}
.scan-action-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-right: 26px;
}
.scan_attendee_profile {
    width: 400px;
    border: 1px solid #dfdfdf;
    padding: 21px;
    margin-top: 12px;
    box-shadow: 1px 0px 6px 0px #ccc;
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
</style>
