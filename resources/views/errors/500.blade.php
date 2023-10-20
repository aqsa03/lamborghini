@extends('errors::minimal')

@section('title', __('Server Error'))
@section('code', '500')
@section('message', (empty($exception?->getMessage())) ? __('Server Error') : $exception->getMessage())
