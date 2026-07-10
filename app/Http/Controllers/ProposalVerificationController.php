<?php

namespace App\Http\Controllers;

use App\Models\Proposal;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Response;
use Illuminate\View\View;

class ProposalVerificationController extends Controller
{
    public function show(string $token): View
    {
        $proposal = Proposal::query()
            ->where('verification_token', $token)
            ->first();

        return view('proposals.verify', [
            'locale'  => 'pt',
            'proposal' => $proposal,
            'status'   => $proposal?->verificationStatus() ?? 'invalid',
        ]);
    }

    public function qr(string $token): Response
    {
        $proposal = Proposal::query()
            ->where('verification_token', $token)
            ->firstOrFail();

        $options = new QROptions([
            'outputType'   => QRCode::OUTPUT_MARKUP_SVG,
            'eccLevel'     => QRCode::ECC_M,
            'scale'        => 8,
            'outputBase64' => false,
        ]);

        $svg = (new QRCode($options))->render($proposal->verificationUrl());

        return response($svg, 200, [
            'Content-Type'  => 'image/svg+xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
