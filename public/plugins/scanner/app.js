var app = new Vue({
  el: '#app',
  data: {
    scanner: null,
    activeCameraId: null,
    cameras: [],
    scans: []
  },
  mounted: function () {
    var self = this;
    self.scanner = new Instascan.Scanner({ video: document.getElementById('preview'), scanPeriod: 1 });
    self.scanner.addListener('scan', function (content, image) {
      self.scans.unshift({ date: +(Date.now()), content: content });
      var audioElement = document.createElement('audio');
      audioElement.setAttribute('src', 'audio/button-11.mp3');
      if (content.match(/^https?:\/\//i)) {
        //window.open(content);
        $("#data-container").attr("src", content+"/"+$("#sede").val()+"/");
        audioElement.play();
        setTimeout(updateSource,600000);
      }
    });
    Instascan.Camera.getCameras().then(function (cameras) {
      self.cameras = cameras;
      if (cameras.length > 0) {
        self.activeCameraId = cameras[0].id;
        self.scanner.start(cameras[0]);
      } else {
        console.error('No se encontro ninguna camara.');
      }
    }).catch(function (e) {
      console.error(e);
    });
  },
  methods: {
    formatName: function (name) {
      return name || 'Camara Detectada';
    },
    selectCamera: function (camera) {
      this.activeCameraId = camera.id;
      this.scanner.start(camera);
    }
  }
});


function updateSource(){
    $("#data-container").attr("src","");
}