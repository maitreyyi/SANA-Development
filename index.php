<?php include $_SERVER["DOCUMENT_ROOT"] . "/template/inc/base.php"; ?>

<!DOCTYPE html>
<html>
	<head>
		
		<?php include $_INCLUDES . "/head.php"; ?>
		
	</head>
	<body>
		
		<?php include $_INCLUDES . "/nav.php"; ?>
		
		<div id="home-page-content" class="page-content">
			<div class="page-content-wrapper">
				<div class="center-content">
					<img src="./img/network-alignment.gif" alt="Network alignment animation" />
				</div>
			</div>
			<div class="page-content-wrapper">
				<p>SANA stands for Simulated Annealing Network Aligner. It takes as input two networks and aligns them (schematically).
<b>SANA is <i>by far</i> the best global network alignment algorithm
out there, and will probably never be beaten because (a) it's fast, and (b) it produces the best answer possible in
cases where we know the correct answer.</b>
We have compared it against <i>every</i> algorithm we've found in the
past decade, and SANA outperforms them all, often by a <i>huge</i> margin.
It may sound like pure chutzpah to say so, but we firmly believe that if you're using anything other than
SANA to align your networks, then you're wasting your time.  I challenge anybody to disprove this statement.</p>

<p>The one available on this website is a bit old and corresponds to the
version from our first paper, which you can read at
<a href="https://doi.org/10.1093/bioinformatics/btx090">BioInformatics</a>, or as a <a
href="https://arxiv.org/abs/1607.02642">preprint</a>.</p>

<p>The most recent version of SANA is always available on <a href="https://github.com/waynebhayes/SANA">github</a>.</p>
<p>The full IID networks used in the paper <i>not available yet</i> are available <a href="SANA+IID.tar.gz">here</a>.</p>

				<div id="home-page-buttons">
					<a class="button radius" href="<?php echo $_URL . '/query'; ?>">Submit New Job</a>
					<a class="button radius" href="<?php echo $_URL . '/results'; ?>">Lookup Previous Job</a>
				</div>
			</div>
			<div class="page-content-wrapper">
				<div class="center-content">
					<img src="./img/xkcd-machine-learning.png" alt="Network alignment cartoon" />
				</div>
			</div>
		</div>
		
		<?php include $_INCLUDES . "/footer.php"; ?>
		
	</body>
</html>
