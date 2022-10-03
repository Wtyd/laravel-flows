<template>
  <div class="container">
    <div class="">
      <label
        >File
        <input id="file" ref="file" type="file" @change="handleFileUpload()" />
      </label>
      <button @click="submitForm()">Submit</button>
    </div>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'InputFile',
  props: {
    endpoint: {
      type: String
    }
  },
  data() {
    return {
      file: ''
    };
  },

  methods: {
    submitForm() {
      const form = new FormData();
      form.append('file', this.file);

      axios
        .post(this.endpoint, form, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        })
        .then(data => {
          console.log(data);
        })
        .catch(err => {
          console.log(err);
        });
    },
    handleFileUpload() {
      const [file] = this.$refs.file.files;
      this.file = file;
    }
  }
};
</script>
