@extends('layout.app')

<link href="{{url('/')}}/assets/vendor/css/frontend.css" rel="stylesheet" />
<style>
    /* Floating GIF */
    .floating-gif {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 100px;
      height: auto;
      z-index: 9999;
      cursor: pointer;
    }

    @media (max-width: 768px) {
      .floating-gif {
            width: 80px;
            bottom: 15px;
            right: 15px;
        }
    }
    .loader {
        width: 50px;
        height: 50px;
        aspect-ratio: 1;
        display: grid;
        border: 4px solid #0000;
        border-radius: 50%;
        border-color: #ccc #0000;
        animation: l16 1s infinite linear;
        margin-left : 45%;
    }
    .loader::before, .loader::after {    
        content: "";
        grid-area: 1/1;
        margin: 2px;
        border: inherit;
        border-radius: 50%;
    }
    .loader::before {
        border-color: #f03355 #0000;
        animation: inherit; 
        animation-duration: .5s;
        animation-direction: reverse;
    }
    .loader::after {
        margin: 8px;
    }
    @keyframes l16 { 
        100%{transform: rotate(1turn)}
    }
</style>
@section('content')
<div class="row">
    <div class="col-md-12 col-lg-12 col-xs-12 nav-div">
        @extends('frontend.navbar')
    </div>
    
    <div class="col-md-12 col-lg-12 col-xs-12 col-sm-12">
        <div class="row">
            <div class="col-md-3 col-lg-3">

            </div>
            <div class="col-md-6 col-lg-6 col-sm-6 col-xs-6">
                <div class="d-flex full-height">
                    <div class="card p-4 shadow-lg order-div">
                        <div class="card-body">
                            @if(session('error'))
                                <div class="alert alert-danger alert-dismissible fade show alert-msg-box" role="alert">
                                    <p class="error-msg">{{ session('error') }}</p>
                                    <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            @if(session('success'))
                                <div class="alert alert-success alert-dismissible fade show alert-msg-box" role="alert">
                                    <p class="success-msg">{!! session('success') !!}</p>
                                    <button type="button" class="btn-close close-msg" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            @endif
                            <h5 class="card-title text-center">Place a new order</h5>
                            <p class="form-instruction">Fields marked with * are mandatory</p>                            
                            <form name="place-order" action="{{route('place_order')}}" method="POST" enctype="multipart/form-data">
                            @csrf
                                <div class="form-group order-form">
                                    <label for="occassion">Occassion <label style="color:red;">*</label></label>
                                    <input type="text" id="occassion" name="occassion" class="form-control" placeholder="e.g. Birthday" value="{{ old('occassion') }}" required>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_type">Cake <label style="color:red;">*</label></label>
                                    <input type="text" id="cake_type" name="cake_type" class="form-control" placeholder="e.g. Chocolate, Vanilla" value="{{ old('cake_type') }}"required>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_flavor">Flavor <label style="color:red;"></label></label>
                                    <input type="text" id="cake_flavor" name="cake_flavor" class="form-control" placeholder="e.g. Vanilla" value="{{ old('cake_flavor') }}">
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_weight">Weight <label style="color:red;">*</label></label>
                                    <input type="text" id="cake_weight" name="cake_weight" class="form-control" placeholder="e.g. 500gm, 1kg. Maximum weight up to 5 kg" value="{{ old('cake_weight') }}" required>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_instruction">Special instruction</label>
                                    <textarea class="form-control" id="cake_instruction" name="cake_instruction" rows="3" placeholder="Write here if you have any special instruction for us" value="{{ old('cake_instruction') }}"></textarea>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_delivery_date">Delivery Date <label style="color:red;">*</label></label>
                                    <input type="date" class="form-control" id="cake_delivery_date" name="cake_delivery_date" value="{{ old('cake_delivery_date') }}"/>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_delivery_time">Delivery Time <label style="color:red;">*</label></label>
                                    <input type="time" class="form-control" id="cake_delivery_time" name="cake_delivery_time" value="{{ old('cake_delivery_time') }}"/>
                                </div>
                                <div class="form-group order-form">
                                    <label for="cake_reference_photo">Upload your design</label>
                                    <input type="file" class="form-control" id="cake_reference_photo" name="image" />
                                </div>
                                <button type="submit" class="btn btn-primary place-order">Place Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-lg-3 position-relative">
                <div class="position-absolute bottom-0 start-0 end-0 bg-light p-2">
                    <div>
                        <p style="float:right; margin-bottom:80px; margin-right:27px;">Order me</p>
                        <img src="{{ asset('assets/vendor/imgs/order1.gif') }}" class="floating-gif" onclick="startRecording()" height="100" width="100" 
                             style="cursor:pointer; float:right;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="voiceOrderListenModal" tabindex="-1" aria-labelledby="voiceOrderListenModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body cannotcancel-body">
        <p id="cannotcancel-msg">Tell me your order. I am listening <img src="{{ asset('assets/vendor/imgs/wave_loading.gif') }}" width="50" height="50"></p>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="orderPlacedModal" tabindex="-1" aria-labelledby="orderPlacedModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body orderplaced-body">
        <p id="orderplaced-msg"></p>
        <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close" style="float:right;">Ok</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="orderPlaceErrorModal" tabindex="-1" aria-labelledby="orderPlaceErrorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="orderPlaceErrorModalLabel">Need some more input from you to place the order</h5>
        </div>
        <div class="modal-body orderplaceerror-body">
            <p id="orderplaceerror-msg"></p>
            <button type="button" class="btn btn-success" data-bs-dismiss="modal" aria-label="Close" style="float:right;">Ok</button>
        </div>
    </div>
  </div>
</div>
<div class="modal fade" id="orderAnalysingModal" tabindex="-1" aria-labelledby="orderAnalysingModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body orderanalysing-body">
        <p id="orderanalysingerror-msg" style="text-align:center;">Analysing the order</p>
        <div class="loader"></div>
      </div>
    </div>
  </div>
</div>
@endsection
<script src="{{url('/')}}/assets/vendor/js/jquery.min.js"></script>
<script src="{{url('/')}}/assets/vendor/js/orderhandler.js"></script>
<script>
    let mediaRecorder;
    let audioChunks = [];
    let micStream;
    let silenceTimer;
    let audioContext;
    let analyser;
    let dataArray;
    let sourceNode;
    let voiceOrderListenModal = '';
    let orderPlacedModal = '';
    let orderPlaceErrorModal = '';
    let orderAnalysingModal = '';

    async function startRecording() {
        micStream = await navigator.mediaDevices.getUserMedia({ audio: true });
        audioChunks = [];
        // Set up MediaRecorder
        mediaRecorder = new MediaRecorder(micStream);
        mediaRecorder.ondataavailable = (e) => audioChunks.push(e.data);
        mediaRecorder.onstop = async () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
            const formData = new FormData();
            formData.append('audio', audioBlob, 'voice.webm');
            // Clean up mic stream
            micStream.getTracks().forEach(track => track.stop());
            // Send to Laravel
            const response = await fetch('/api/v1/voice-order', {
                method: 'POST',
                body: formData
            });
            const result = await response.json();
            //document.getElementById('transcript').innerText = result.transcript ?? 'Transcription failed';
            orderAnalysingModal.hide();
            if(result.status == 'error') {
                $('#orderplaceerror-msg').text(result.message);
                orderPlaceErrorModal = new bootstrap.Modal(document.getElementById('orderPlaceErrorModal'));
                orderPlaceErrorModal.show();
            } else if(result.status == 'success'){
                $('#orderplaced-msg').text(`Your order is placed with Order No. ${result.order_no}`);
                orderPlacedModal = new bootstrap.Modal(document.getElementById('orderPlacedModal'));
                orderPlacedModal.show();
            }
        };
        mediaRecorder.start();
        $('#orderplaceerror-msg').text('');
        voiceOrderListenModal = new bootstrap.Modal(document.getElementById('voiceOrderListenModal'));
        voiceOrderListenModal.show();
        // Start analyzing audio volume
        setupAudioProcessing(micStream);
    }

    function setupAudioProcessing(stream) {
        audioContext = new AudioContext();
        analyser = audioContext.createAnalyser();
        sourceNode = audioContext.createMediaStreamSource(stream);
        sourceNode.connect(analyser);
        analyser.fftSize = 2048;
        const bufferLength = analyser.frequencyBinCount;
        dataArray = new Uint8Array(bufferLength);
        // Start checking volume
        checkSilence();
    }

    function checkSilence() {
        analyser.getByteTimeDomainData(dataArray);
        // Compute average volume
        let sum = 0;
        for(let i = 0; i < dataArray.length; i++) {
            const val = dataArray[i] - 128;
            sum += val * val;
        }
        const volume = Math.sqrt(sum / dataArray.length);
        // If volume is low for 2+ seconds, stop recording
        if(volume < 5) {
            if(!silenceTimer) {
                silenceTimer = setTimeout(() => {
                    stopRecording();
                }, 3000);
            }
        } else {
            clearTimeout(silenceTimer);
            silenceTimer = null;
        }
        requestAnimationFrame(checkSilence);
    }

    function stopRecording() {
        if(mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            voiceOrderListenModal.hide();
            orderAnalysingModal = new bootstrap.Modal(document.getElementById('orderAnalysingModal'));
            orderAnalysingModal.show();
        }
        // Stop audio analysis
        if (audioContext) {
            audioContext.close();
        }
    }
</script>