@extends('layouts.scannerManual')
@section('title', 'QR Scanner')
@section('content')

<div id="app">
    <div class="sidebar">
        <section class="cameras">
            <ul>
                <li v-if="cameras.length === 0" class="empty" style="color: #FFF;">No se detecto ninguna camara</li>
                <li v-for="camera in cameras">
                    <span v-if="camera.id == activeCameraId" :title="formatName(camera.name)" class="active">@{{ formatName(camera.name) }}</span>
                    <span v-if="camera.id != activeCameraId" :title="formatName(camera.name)">
                        <a @click.stop="selectCamera(camera)">@{{ formatName(camera.name) }}</a>
                    </span>
                </li>  
                <img src="{{ asset('img/LOGO DG DIAPO.png') }}" style="float: right;margin-right: 20px;margin-top: -25px;margin-bottom: 5px;"/>
            </ul>

        </section>
        <!--<section class="scans">
          <h2>Scans</h2>
          <ul v-if="scans.length === 0">
            <li class="empty">No scans yet</li>
          </ul>
          <transition-group name="scans" tag="ul">
            <li v-for="scan in scans" :key="scan.date" :title="scan.content">@{{ scan.content }}</li>
          </transition-group>
        </section>-->
    </div>
    <div class="preview-container">
        <video id="preview"></video>  <iframe src="" id="data-container" frameborder="0"></iframe> 
    </div>

    <input type="hidden" id="sede" value="{{ $sede }}"/>
</div>
@endsection
