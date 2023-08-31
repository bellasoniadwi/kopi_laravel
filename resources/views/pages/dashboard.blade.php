@extends('newlayout.main')

@section('title')
    Dashboard
@endsection

@section('dashboard', 'active bg-gradient-kopi')

@section('content')
  <div class="row min-vh-80">
    <div class="col-12">
      <div class="card h-100">
        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
          <div class="bg-gradient-kopi shadow-kopi border-radius-lg pt-4 pb-3">
            <h5 class="text-white text-capitalize ps-3">Persebaran Kopi</h5>
          </div>
        </div>
        <div class="card-body">
          <div id="vector-map" class="mt-5 min-height-500"></div>
        </div>
      </div>
    </div>
  </div>

@endsection
@section('js')
<script src="{{ asset('assets/js/plugins/world.js')}}"></script>
    <script>
      var map = new jsVectorMap({
        selector: "#vector-map",
        map: "world_merc",
        zoomOnScroll: false,
        zoomButtons: false,
        selectedMarkers: [1, 3],
        markersSelectable: true,
        markers: [{
            name: "USA",
            coords: [40.71296415909766, -74.00437720027804]
          },
          {
            name: "Germany",
            coords: [51.17661451970939, 10.97947735117339]
          },
          {
            name: "Brazil",
            coords: [-7.596735421549542, -54.781694323779185]
          },
          {
            name: "Russia",
            coords: [62.318222797104276, 89.81564777631716]
          },
          {
            name: "China",
            coords: [22.320178999475512, 114.17161225541399],
            style: {
              fill: '#E91E63'
            }
          }
        ],
        markerStyle: {
          initial: {
            fill: "#e91e63"
          },
          hover: {
            fill: "E91E63"
          },
          selected: {
            fill: "E91E63"
          }
        },
      });
    </script>
@endsection
