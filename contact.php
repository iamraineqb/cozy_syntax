<?php
// 1. Connect to the database
require_once 'db.php';

$messageAlert = '';
$messageType = '';

// 2. Process the contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Clean inputs
    $handle = trim($_POST['handle']);
    $status = trim($_POST['status']);
    $msg = trim($_POST['message']);

    // Validate required fields
    if (!empty($handle) && !empty($msg)) {
        try {
            // 3. Insert into the inquiries table
            $stmt = $pdo->prepare("INSERT INTO inquiries (handle, status, message) VALUES (:handle, :status, :message)");
            $stmt->execute([
                'handle' => $handle,
                'status' => $status,
                'message' => $msg
            ]);
            
            $messageAlert = "// Transmission successful. Thank you for the log entry. ✨";
            $messageType = "success";
        } catch(PDOException $e) {
            $messageAlert = "// System Error: " . $e->getMessage();
            $messageType = "error";
        }
    } else {
        $messageAlert = "// Error: Handle and Message fields are required.";
        $messageType = "error";
    }
}

// Include the top navigation
include 'includes/header.php';
?>

<!-- Contact Form UI -->
<section class="min-h-screen flex items-center justify-center px-6 py-20 z-10 relative">
    <div class="max-w-3xl mx-auto w-full">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold mb-3 text-white font-robot">Ping The Server</h2>
            <p class="text-indigo-200/70 font-light">Leave anonymous feedback, feature requests, or just drop a cozy hello.</p>
        </div>
        
        <div class="glass-card p-8 md:p-10 rounded-3xl relative overflow-hidden shadow-2xl">
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Dynamic PHP Alert Messages -->
            <?php if ($messageAlert): ?>
                <div class="mb-6 p-4 rounded-xl text-sm font-code text-center backdrop-blur-md relative z-10 <?php echo $messageType === 'success' ? 'bg-emerald-900/40 border border-emerald-500/30 text-emerald-300' : 'bg-rose-900/40 border border-rose-500/30 text-rose-300'; ?>">
                    <?php echo $messageAlert; ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="contact.php" class="space-y-6 relative z-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-xs font-code font-bold uppercase text-purple-300">Your Alias</label>
                        <!-- Added name="handle" -->
                        <input type="text" name="handle" required placeholder="e.g. CodeExplorer" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 placeholder-indigo-400/30 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-xs font-code font-bold uppercase text-purple-300">Current Status (Optional)</label>
                        <!-- Added name="status" -->
                        <input type="text" name="status" placeholder="e.g. Debugging late at night" class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 placeholder-indigo-400/30 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code">
                    </div>
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-code font-bold uppercase text-purple-300">Message / Log Entry</label>
                    <!-- Added name="message" -->
                    <textarea name="message" rows="4" required placeholder="Type something cozy..." class="w-full bg-slate-950/50 border border-indigo-900/50 rounded-xl px-4 py-3 text-sm text-indigo-100 placeholder-indigo-400/30 focus:outline-none focus:border-purple-500 focus:ring-1 focus:ring-purple-500 transition-all font-code resize-none"></textarea>
                </div>
                <button type="submit" class="w-full py-3.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl text-sm font-bold hover:opacity-90 transition-all shadow-lg shadow-purple-900/30 cursor-pointer font-code uppercase tracking-wider">
                    Transmit Log
                </button>
            </form>
        </div>
    </div>
</section>

<?php 
// Include the bottom layout
include 'includes/footer.php'; 
?>