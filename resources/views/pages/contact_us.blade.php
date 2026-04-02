@extends('layouts.app')

@section('title', $page->title)

@section('css')
    <style>
        @import url(//fonts.googleapis.com/css?family=Montserrat:300,400,500);

        .contact2 {
            color: #8d97ad;
            font-weight: 300;
            padding: 60px 0;
            margin-bottom: 150px;
            background-position: center top;
        }

        .contact2 h1,
        .contact2 h2,
        .contact2 h3,
        .contact2 h4,
        .contact2 h5,
        .contact2 h6 {
            color: #3e4555;
        }

        .contact2 .font-weight-medium {
            font-weight: 500;
        }

        .contact2 .subtitle {
            color: #8d97ad;
            line-height: 24px;
        }

        .contact2 .bg-image {
            background-size: cover;
            position: relative;
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
        }

        .contact2 .card.card-shadow {
            -webkit-box-shadow: 0px 0px 30px rgba(115, 128, 157, 0.1);
            box-shadow: 0px 0px 30px rgba(115, 128, 157, 0.1);
        }

        /* .contact2 .detail-box .round-social {
                            margin-top: 100px;
                        } */

        .contact2 .round-social a {
            background: transparent;
            margin: 0 7px;
            padding: 11px 12px;
        }

        .contact2 .contact-container .links a {
            color: #8d97ad;
        }

        .contact2 .contact-container {
            position: relative;
            top: 150px;
        }

        .contact2 .btn-green-gradiant {
            background: #5BBA6F;
            background: -webkit-linear-gradient(legacy-direction(to right), #5BBA6F 0%, #428851e5 100%);
            background: -webkit-gradient(linear, left top, right top, from(#5BBA6F), to(#428851e5));
            background: -webkit-linear-gradient(left, #5BBA6F 0%, #428851e5 100%);
            background: -o-linear-gradient(left, #5BBA6F 0%, #428851e5 100%);
            background: linear-gradient(to right, #5BBA6F 0%, #428851e5 100%);
        }

        .contact2 .btn-green-gradiant:hover {
            background: #428851e5;
            background: -webkit-linear-gradient(legacy-direction(to right), #428851e5 0%, #5BBA6F 100%);
            background: -webkit-gradient(linear, left top, right top, from(#428851e5), to(#5BBA6F));
            background: -webkit-linear-gradient(left, #428851e5 0%, #5BBA6F 100%);
            background: -o-linear-gradient(left, #428851e5 0%, #5BBA6F 100%);
            background: linear-gradient(to right, #428851e5 0%, #5BBA6F 100%);
        }

        .contact2 .container .center-heading {
            text-align: center;
            position: relative;
            top:100px;
            font-size: 50px;
        }
    </style>
@endsection

@section('background-color', 'bg-container')

@section('content')
    <div>

        <div class="contact2"
            style="background-image: url('{{ asset('images/map.png') }}'); background-size:cover;"
            id="contact">

            <div class="container">
                <div >
                    <h1 class="center-heading">Contact Us</h1>
                </div>
                <div class="row contact-container">
                    <div class="col-lg-12">
                        <div class="card card-shadow border-0 mb-4">
                            <div class="row m-0">
                                <div class="col-lg-8 p-0">
                                    <div class="contact-box p-4">
                                        <h4 class="title">Message Us</h4>
                                        <form>
                                            <div class="row">
                                                <div class="col-lg-6">
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="text" placeholder="Name">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="text" placeholder="Email">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="text" placeholder="Phone">
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="form-group mt-3">
                                                        <input class="form-control" type="text" placeholder="Location">
                                                    </div>
                                                </div>
                                                <div class="col-lg-12">
                                                    <div class="form-group mt-3">
                                                        <textarea class="form-control" placeholder="Message"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-lg-12 text-end">
                                                    <button type="submit"
                                                        class="btn btn-green-gradiant mt-3 mb-3 text-white border-0 py-2 px-3"><span>
                                                            SUBMIT NOW <i class="ti-arrow-right"></i></span></button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-lg-4 bg-text-theme">
                                    <div class="detail-box p-4">
                                        <h5 class="text-dark font-weight-light mb-3">Address</h5>
                                        <p class="text-dark op-7">
                                            {!! $page->extras['address'] !!} (<a href="{{ $page->extras['maps'] }}" target="_blank">Google Maps</a>)
                                        </p>
                                        <h5 class="text-dark font-weight-light mb-3 mt-4">Call Us</h5>
                                        <p class="text-dark op-7">
                                            {!! $page->extras['phone'] !!}
                                        </p>
                                        <h5 class="text-dark font-weight-light mb-1 mt-4">Email</h5>
                                        <p class="text-dark op-7">
                                            {!! $page->extras['email'] !!}
                                        </p>
                                        {{-- <div class="round-social light">
                                            @if ($page->extras['facebook'])
                                                <a href="{{ $page->extras['facebook'] }}"
                                                    class="ml-0 text-decoration-none text-dark border border-dark rounded-circle"><i
                                                        class="bi bi-facebook"></i></a>
                                            @endif

                                            @if ($page->extras['twitter'])
                                                <a href="{{ $page->extras['twitter'] }}"
                                                    class="ml-0 text-decoration-none text-dark border border-dark rounded-circle"><i
                                                        class="bi bi-twitter-x"></i></a>
                                            @endif

                                        </div> --}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
