<!-- Series count -->
<div class="card">
    <div class="card-header">
        Series
    </div>
    <div class="card-body">
        <p class="count">Series overview already has</p>
        <h2><?php echo count_series($pdo) ?></h2>
        <p>series listed</p>
        <a href="/DDWT21/week1/add/" class="btn btn-primary">List yours</a>
    </div>
</div>
