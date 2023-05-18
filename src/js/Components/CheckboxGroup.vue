<template>
    <div style="padding: 15px 0px">
        <h2 style="min-width: 175px; font-weight: 500px;">{{ property.name }}</h2>
        <div style="margin-bottom: 5px;" class="wpf-form-group">
            <el-checkbox :indeterminate="indeterminate" v-model="checkAll" @change="handleCheckAllChange">
                <span style="font-weight:700">Check all</span>
            </el-checkbox>
        </div>
        <div class="wpf-form-group">
            <el-checkbox-group v-model="checkList" @change="updateCheckList">
                <el-checkbox v-for="(role, role_key) in property.capabilities" :label="role_key" :key="role_key">
                    {{ removeUnderlineAndCapitalize(role_key) }}
                </el-checkbox>
            </el-checkbox-group>
        </div>
    </div>
</template>

<script>
export default {
    name: 'CheckboxGroup',
    data() {
        return {
            checkList: [],
            checkAll: false,
            indeterminate: false,
        };
    },
    props: {
        property: {
            type: Object,
            default: () => { }
        },
        userIndex: {
            type: String,
            default: () => ''
        },
        paymatticCustomUser: {
            type: Object,
            default: () => { }
        },
    },
    methods: {
        removeUnderlineAndCapitalize(str) {
            str = str.replace(/_/g, ' ');
            str = str.charAt(0).toUpperCase() + str.slice(1);
            return str;
        },
        updateCheckList() {
            let self = this;
            Object.keys(self.paymatticCustomUser[self.userIndex]?.capabilities).forEach(function (key, index) {
                if (self.checkList.includes(key)) {
                    self.paymatticCustomUser[self.userIndex].capabilities[key] = true;
                } else {
                    self.paymatticCustomUser[self.userIndex].capabilities[key] = false;
                }
            });
            if (self.checkList.length == Object.keys(self.property.capabilities).length) {
                self.indeterminate = false;
                self.checkAll = true;
            } else if (self.checkList.length > 0) {
                self.indeterminate = true;
                self.checkAll = false;
            } else {
                self.indeterminate = false;
                self.checkAll = false;
            }
        },
        handleCheckAllChange (isChecked) {
            let self = this;
           if (isChecked) {
                self.checkList = [];
                Object.keys(self.paymatticCustomUser[self.userIndex]?.capabilities).forEach(function (key, index) {
                    self.paymatticCustomUser[self.userIndex].capabilities[key] = true;
                    self.indeterminate = false;
                    self.checkList.push(key);
                    self.checkAll = true;
                });
            } else {
                Object.keys(self.paymatticCustomUser[self.userIndex]?.capabilities).forEach(function (key, index) {
                    self.paymatticCustomUser[self.userIndex].capabilities[key] = false;
                    self.checkList = [];
                    self.checkAll = false;
                });
            }
            // this.checkList = val ? this.property.capabilities : [];
        },
        // handleCheckedCapabilitiesChange (value) {
        //     let checkedCount = value.length;
        //     this.checkAll = checkedCount === this.property.capabilities.length;
        //     this.isIndeterminate = checkedCount > 0 && checkedCount < this.property.capabilities.length;
        // },
    },
    mounted() {
        let self = this;
        let checkStatus = [];
        Object.keys(this.property.capabilities).forEach(function (key, index) {
            console.log(key, self.property.capabilities[key]);
            if (self.property.capabilities[key]) {
                checkStatus.push(self.property.capabilities[key]);
                self.indeterminate = true;
                self.checkList.push(key);
            }
        });
        if (checkStatus.length == Object.keys(this.property.capabilities).length) {
            self.indeterminate = false;
            self.checkAll = true;
        }
    },
}
</script>