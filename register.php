<?php
// 1. Connect to the database
require_once 'db.php';

$message = '';
$messageType = '';

// 2. Check if the form was submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean the input data
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (!empty($username) && !empty($password)) {
        // Validation: Username format check (3-20 chars, letters, numbers, underscores only)
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $username)) {
            $message = "// Error: Username must be 3-20 characters long (letters, numbers, underscores only).";
            $messageType = "error";
        } 
        // Validation: Strong password check (Min 8 chars, must contain letters and numbers)
        elseif (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
            $message = "// Error: Password must be at least 8 characters and include both letters and numbers.";
            $messageType = "error";
        } else {
            try {
                // 3. Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // 4. Insert the new user into the database
                $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
                $stmt->execute(['username' => $username, 'password' => $hashed_password]);

                $message = "// Registration successful! Redirecting to login...";
                $messageType = "success";
                
                // 5. FAST redirect to login page after just 1 second!
                header("Refresh: 1; url=login.php");
            } catch (PDOException $e) {
                // Check if the error is a duplicate username (Code 23000)
                if ($e->getCode() == 23000) {
                    $message = "// Error: That handle is already taken. Choose another.";
                } else {
                    $message = "// System Error: " . $e->getMessage();
                }
                $messageType = "error";
            }
        }
    } else {
        $message = "// Error: Please fill in all required fields.";
        $messageType = "error";
    }
}

// Include the top navigation (this has your smooth fade-in animation!)
include 'includes/header.php';
?>

<!-- Registration Form UI -->
<section class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative">
    <div class="max-w-sm w-full mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-2 text-white font-robot">Register Alias</h2>
            <p class="text-indigo-200/70 font-light text-sm">Create your secure access token.</p>
        </div>
        
        <div class="glass-card p-8 rounded-3xl relative overflow-hidden shadow-2xl">
            
            <!-- Dynamic PHP Alert Messages -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-code text-center backdrop-blur-md <?php echo $messageType === 'success' ? 'bg-emerald-900/40 border border-emerald-500/30 text-emerald-300' : 'bg-rose-900/40 border border-rose-500/30 text-rose-300'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="register.php" class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <label class="block text-xs font-code font-bold uppercase text-purple-300">Username</label>
                    <input type="text" name="username" required placeholder="e.g. CodeNinja" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 font-code transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-code font-bold uppercase text-purple-300">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 font-code transition-all">
                    <p class="text-[10px] text-indigo-300/60 font-code">Must be 8+ chars with letters & numbers.</p>
                </div>
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-bold hover:opacity-90 shadow-lg cursor-pointer font-code uppercase tracking-wider transition-all">
                    Initialize Account
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs font-code text-indigo-300/70">
                Already have an access token? <a href="login.php" class="text-purple-400 hover:text-purple-300 transition-colors">Login here.</a>
            </div>
        </div>
    </div>
</section>

<?php 
// Include the bottom layout
include 'includes/footer.php'; 
?>