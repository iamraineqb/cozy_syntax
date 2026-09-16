<?php
// 1. Start the session to remember the logged-in user
session_start();

// 2. Connect to the database
require_once 'db.php';

$message = '';
$messageType = '';

// 3. Process the login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            // Fetch the user from the database
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute(['username' => $username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the password matches the hash in the database
            if ($user && password_verify($password, $user['password'])) {
                
                // Password is correct! Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                
                $message = "// Access Granted. Redirecting to Sanctuary...";
                $messageType = "success";
                
                // Redirect to the home page after 2 seconds
                header("Refresh: 2; url=index.php");
            } else {
                $message = "// Error: Invalid alias or password.";
                $messageType = "error";
            }
        } catch (PDOException $e) {
            $message = "// System Error: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $message = "// Error: Please provide both credentials.";
        $messageType = "error";
    }
}

// Include the top navigation
include 'includes/header.php';
?>

<!-- Login Form UI -->
<section class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative">
    <div class="max-w-sm w-full mx-auto">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold mb-2 text-white font-robot">System Login</h2>
            <p class="text-indigo-200/70 font-light text-sm">Enter your credentials to continue.</p>
        </div>
        
        <div class="glass-card p-8 rounded-3xl relative overflow-hidden shadow-2xl">
            
            <!-- Dynamic PHP Alert Messages -->
            <?php if ($message): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-code text-center backdrop-blur-md <?php echo $messageType === 'success' ? 'bg-emerald-900/40 border border-emerald-500/30 text-emerald-300' : 'bg-rose-900/40 border border-rose-500/30 text-rose-300'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="space-y-6 relative z-10">
                <div class="space-y-2">
                    <label class="block text-xs font-code font-bold uppercase text-purple-300">Username</label>
                    <input type="text" name="username" required placeholder="e.g. CodeNinja" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 font-code transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-code font-bold uppercase text-purple-300">Password</label>
                    <input type="password" name="password" required placeholder="••••••••" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 font-code transition-all">
                </div>
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-bold hover:opacity-90 shadow-lg cursor-pointer font-code uppercase tracking-wider transition-all">
                    Establish Connection
                </button>
            </form>
            
            <div class="mt-6 text-center text-xs font-code text-indigo-300/70">
                No access token? <a href="register.php" class="text-purple-400 hover:text-purple-300 transition-colors">Register here.</a>
            </div>
        </div>
    </div>
</section>

<?php 
// Include the bottom layout
include 'includes/footer.php'; 
?>