<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-icon-box" style="background: #f0fdf4; color: #16a34a;">
                <i class="fa-solid fa-user-plus"></i>
            </div>
            <h2>Create Account</h2>
            <p>Join the Inventory Management System</p>
        </div>

        <?php if($this->session->flashdata('error')): ?>
            <div class="auth-alert alert-danger">
                <i class="fa-solid fa-circle-exclamation"></i> <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/register_process') ?>" method="post" class="auth-form">
            <div class="form-group-block">
                <label><i class="fa-solid fa-id-card"></i> Full Name</label>
                <input type="text" name="full_name" class="auth-input" placeholder="e.g., Juan Dela Cruz" required autocomplete="off">
            </div>
            <div class="form-group-block">
                <label><i class="fa-solid fa-user"></i> Choose Username</label>
                <input type="text" name="username" class="auth-input" placeholder="e.g., admin_shoes" required autocomplete="off">
            </div>
            
            <div class="form-group-block">
                <label><i class="fa-solid fa-lock"></i> Password</label>
                <input type="password" name="password" class="auth-input" placeholder="Create strong password" required>
            </div>

            <button type="submit" class="auth-btn-primary">
                Register Account <i class="fa-solid fa-user-plus"></i>
            </button>
        </form>
        
        <div class="auth-footer">
            <p>Already have an account? <a href="<?= base_url('login') ?>">Login here</a></p>
        </div>
    </div>
</div>