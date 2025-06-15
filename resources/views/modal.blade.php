<div id="modal" _="on closeModal add .closing then wait for animationend then remove me">
	<div class="modal-underlay" _="on click trigger closeModal"></div>
	<div class="modal-content" style="overflow: scroll">
        <div class="body">
		    <h3>{{ $title }}</h3>
		    {!! $content !!}
        </div>
        <br>
        <br>
        <button class="btn danger" _="on click trigger closeModal">Close</button>
	</div>
</div>
