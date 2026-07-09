<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-box">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <h2>IMS Training</h2>
            <p>Sign in to manage your tracks and inventories</p>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="auth-alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>
        
        <?php if($this->session->flashdata('success')): ?>
            <div class="auth-alert alert-success">
                <i class="fa-solid fa-circle-check"></i> <?= $this->session->flashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/login_process') ?>" method="post" class="auth-form">
            <div class="form-group-block">
                <label><i class="fa-solid fa-user"></i> Username</label>
                <input type="text" name="username" class="auth-input" placeholder="Enter your username" required autocomplete="off">
            </div>
            
            <div class="form-group-block">
                <label><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" name="password" class="auth-input" placeholder="••••••••" required>
            </div>
            
            <button type="submit" class="auth-btn-primary">
                Login Account <i class="fa-solid fa-arrow-right-to-bracket"></i>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Do not have an account? <a href="<?= base_url('register') ?>">Register here</a></p>
        </div>
    </div>
</div>