		
		<footer class="px-4 py-3 bg-white fixed-bottom w-100">
			<div class="float-right d-none d-sm-block">
				<b>Version</b> 1.0.0
			</div>
			Copyright &copy; {{ date('Y') }}. All rights reserved.
		</footer>

	</div>
	<script src="{{ url('assets') }}/js/jquery.min.js"></script>
	<script src="{{ url('assets') }}/js/bootstrap.bundle.min.js"></script>
	<script src="{{ url('assets') }}/js/jquery.form.min.js"></script>
	<script src="{{ url('/assets/custom.js') }}?update={{ time() }}"></script>
</body>
</html>
