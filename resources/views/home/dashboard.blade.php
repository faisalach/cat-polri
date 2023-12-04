@include('partials.header')
<div class="card">
	<div class="card-body">
		<div class="row">
			<div class="col-md-6 col-lg-4 col-12">
				<div class="info-box">
					<div class="info-box-icon bg-info elevation-1">
						<span>{{ $count_siswa }}</span>
					</div>
					<div class="info-box-content">
						<span class="info-box-text">Jumlah Siswa</span>
						<!-- <span class="info-box-number">3</span> -->
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 col-12">
				<div class="info-box">
					<div class="info-box-icon bg-primary elevation-1">
						<span>{{ $count_pengajar }}</span>
					</div>
					<div class="info-box-content">
						<span class="info-box-text">Jumlah Guru</span>
						<!-- <span class="info-box-number">3</span> -->
					</div>
				</div>
			</div>
			<div class="col-md-6 col-lg-4 col-12">
				<div class="info-box">
					<div class="info-box-icon bg-warning elevation-1">
						<span>{{ $count_test }}</span>
					</div>
					<div class="info-box-content">
						<span class="info-box-text">Jumlah Tes</span>
						<!-- <span class="info-box-number">3</span> -->
					</div>
				</div>
			</div>

			<div class="col-md-6 col-12">
				<div class="info-box">
					<div class="info-box-icon bg-success elevation-1">
						<span>{{ $count_ms }}</span>
					</div>
					<div class="info-box-content">
						<span class="info-box-text">Jumlah Memenuhi Syarat</span>
						<!-- <span class="info-box-number">3</span> -->
					</div>
				</div>
			</div>
			<div class="col-md-6 col-12">
				<div class="info-box">
					<div class="info-box-icon bg-danger elevation-1">
						<span>{{ $count_tms }}</span>
					</div>
					<div class="info-box-content">
						<span class="info-box-text">Jumlah Tidak Memenuhi Syarat</span>
						<!-- <span class="info-box-number">3</span> -->
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
@include('partials.footer')