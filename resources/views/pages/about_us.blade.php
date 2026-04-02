@extends('layouts.app')

@section('title', $page->title)

@section('css')
    <style>
        @import url(//fonts.googleapis.com/css?family=Montserrat:300,400,500);

        .contact2 {
            color: #8d97ad;
            font-weight: 300;
            padding: 60px 0;
            margin-bottom: 70px;
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

        .section-title {
            position: relative;
            margin-bottom: 50px;
        }

        .section-title::after {
            content: '';
            width: 80px;
            height: 4px;
            background: var(--theme-focus);
            position: absolute;
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            border-radius: 2px;
        }
    </style>
@endsection

@section('background-color', 'bg-container')

@section('content')
    <div>

        <div class="contact2" id="about">
            <div class="contact2-banner">
            </div>
            <div class="container ">
                <div>
                    <h1 class="text-center section-title display-5 fw-bold" >About</h1>
                </div>
                <div class="row about-container pt-5">
                    <div class="col-lg-12">
                        <div class="border-0 ">
                            <div class="row m-0 rounded  align-items-center">
                                <div class=" p-0">
                                    <div class="about-box p-5">
                                        <h1 class="text-center display-6 fw-bold">{!! $page->extras['about_us_header'] !!}</h1>
                                        <hr class="" style="border: 2px solid var(--theme-focus);">
                                        <p class="text-center">
                                            {!! $page->extras['about_us_content'] !!}
                                        </p>
                                    </div>
                                </div>
                                <div class="col-lg-6 ">
                                    <div class="detail-box p-4">
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="row m-0 rounded">
                                <div class="col ">

                                    <div class="row p-5 mb-3 bg-text-theme rounded">
                                        <div class="col ">
                                            <h2 class=" h1">Mission</h2>
                                            <hr class="">
                                            <h3 class="">{!! $page->extras['mission_header'] !!}</h3>
                                            <p>{!! $page->extras['mission_content'] !!}</p>
                                        </div>
                                    </div>

                                    <div class="row p-5 mb-3 bg-text-theme card rounded">
                                        <div class="col ">
                                            <h2 class="h1">Vision</h2>
                                            <hr>
                                            <h3>{!! $page->extras['vision_header'] !!}</h3>
                                            <p>{!! $page->extras['vision_content'] !!}</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="container bg-text-theme px-0 border mt-2 rounded">
                            <h1 class="text-center bg-green m-2 p-3 rounded">Contact Us</h1>
                            <div class="card m-2 p-3 px-5 text-center">
                                <div class="p-5">
                                    <h3>{!! $page->extras['cta_header'] !!}</h3>
                                    <p>{!! $page->extras['cta_content'] !!}</p>
                                    <a class="btn bg-text-theme border-success m-auto rounded-pill text-nowrap" href="/">Click Here</a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection
