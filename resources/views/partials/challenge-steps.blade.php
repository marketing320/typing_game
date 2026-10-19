<ol class="flow-steps" aria-label="Challenge progress">
    <li @if($currentStep === 1) aria-current="step" @endif><span>1</span> Your details</li>
    <li @if($currentStep === 2) aria-current="step" @endif><span>2</span> Verify</li>
    <li @if($currentStep === 3) aria-current="step" @endif><span>3</span> Play</li>
</ol>
