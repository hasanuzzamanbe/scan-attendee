<template>
    <div>
        <el-container>
            <el-header>
                <h1>Settings</h1>
            </el-header>
            <form enctype="multipart/form-data" id="upload_json_form">
                <input type="hidden" name="action" value="wppayform_global_tools"/>
                <input type="hidden" name="route" value="upload_csv"/>
                <label>
                    Upload .csv file
                    <input required type="file" name="import_file"/>
                </label>
                <el-button v-loading="uploadingForm" @click="importForm" type="primary" size="mini" class="wpf_new_ui_button">Import Attendees</el-button>
            </form>
      </el-container>
    </div>
</template>
<script>
export default {
    name: 'Settings',
    data() {
        return {
            uploadingForm: false
        }
    },
    methods: {
        importForm() {
                const that = this;
                this.$upload(
                    {
                        action: 'scan_attendee_admin_ajax',
                        route: 'upload_csv',
                    },
                    new FormData(jQuery('#upload_json_form')[0])
                )
                // .then(response => {
                //     that.$notify.success({
                //         title: 'Success!',
                //         message: response.message,
                //         offset: 50,
                //         position: 'bottom-right'
                //     });
    
                //     that.last_uploaded_form = response.form;
                //     that.edit_url = response.edit_url;
                //     jQuery('#upload_json_form')[0].reset();
                //     that.uploadingForm = false;
                // })
                // .catch(error => {
                //     this.$notify.warning({
                //         message: error.responseJSON.message,
                //         offset: 30,
                //         position: 'bottom-right'
                //     });
                //     that.uploadingForm = false;
                // });
            }
    }
}
</script>

