<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: ../index.php");
    exit();
}

include("../includes/header.php");

$complaintId = $_GET["id"] ?? "FX-1024";
$showMessage = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $showMessage = true;
}
?>

<div class="page-header">
    <div>
        <p class="page-eyebrow">Warden Panel</p>
        <h1>Complaint Details</h1>
        <p>Review, approve and assign student complaints.</p>
    </div>

    <a href="complaints.php" class="btn btn-secondary">
        ← Back to Complaints
    </a>
</div>

<?php if ($showMessage): ?>
    <div class="success-message">
        Complaint action saved successfully.
        <span>This is a frontend demo. Backend integration will be added later.</span>
    </div>
<?php endif; ?>

<div class="details-layout">

    <div class="details-main">

        <div class="content-card">
            <div class="card-heading-row">
                <div>
                    <span class="status-badge pending">Pending Review</span>
                    <h2>Water leakage in bathroom</h2>
                    <p class="muted-text">Complaint ID: <?php echo htmlspecialchars($complaintId); ?></p>
                </div>

                <div class="priority-label high">
                    High Priority
                </div>
            </div>

            <div class="detail-grid">
                <div class="detail-item">
                    <span class="detail-label">Student Name</span>
                    <strong>Arun Kumar</strong>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Register Number</span>
                    <strong>21CS1045</strong>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Room Number</span>
                    <strong>B-204</strong>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Block</span>
                    <strong>Block B</strong>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Complaint Category</span>
                    <strong>Plumbing</strong>
                </div>

                <div class="detail-item">
                    <span class="detail-label">Submitted On</span>
                    <strong>06 September 2026</strong>
                </div>
            </div>

            <div class="description-section">
                <h3>Complaint Description</h3>

                <div class="description-box">
                    There is continuous water leakage near the bathroom tap.
                    The floor becomes wet frequently and it may cause students
                    to slip. Kindly arrange maintenance support as soon as possible.
                </div>
            </div>
        </div>

        <div class="content-card">
            <div class="section-heading">
                <div>
                    <h2>Warden Action</h2>
                    <p>Update the complaint review status and assign maintenance.</p>
                </div>
            </div>

            <form method="POST">

                <div class="form-group">
                    <label for="review_status">Review Status</label>

                    <select name="review_status" id="review_status" class="form-control">
                        <option value="pending">Pending Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                        <option value="forwarded">Forwarded to Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="maintenance_team">Assign Maintenance Team</label>

                    <select name="maintenance_team" id="maintenance_team" class="form-control">
                        <option value="">Select maintenance team</option>
                        <option value="plumbing">Plumbing Team</option>
                        <option value="electrical">Electrical Team</option>
                        <option value="carpentry">Carpentry Team</option>
                        <option value="cleaning">Cleaning Team</option>
                        <option value="general">General Maintenance Team</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="warden_remarks">Warden Remarks</label>

                    <textarea
                        name="warden_remarks"
                        id="warden_remarks"
                        class="form-control"
                        rows="5"
                        placeholder="Enter your remarks..."
                    ></textarea>
                </div>

                <div class="action-buttons">
                    <button type="submit" class="btn btn-primary">
                        Save Action
                    </button>

                    <a href="complaints.php" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>

            </form>
        </div>

    </div>

    <div class="details-side">

        <div class="content-card">
            <h3>Complaint Timeline</h3>

            <div class="timeline">
                <div class="timeline-item completed">
                    <span class="timeline-dot"></span>
                    <div>
                        <strong>Complaint Submitted</strong>
                        <p>06 Sep 2026, 09:15 AM</p>
                    </div>
                </div>

                <div class="timeline-item active">
                    <span class="timeline-dot"></span>
                    <div>
                        <strong>Waiting for Warden Review</strong>
                        <p>Current stage</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <span class="timeline-dot"></span>
                    <div>
                        <strong>Maintenance Assigned</strong>
                        <p>Not assigned yet</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <span class="timeline-dot"></span>
                    <div>
                        <strong>Complaint Resolved</strong>
                        <p>Not completed</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-card">
            <h3>AI Prediction</h3>

            <div class="ai-prediction-box">
                <span class="ai-icon">✦</span>

                <div>
                    <strong>Predicted Category</strong>
                    <p>Plumbing</p>
                </div>
            </div>

            <div class="prediction-row">
                <span>Confidence</span>
                <strong>94%</strong>
            </div>

            <div class="prediction-row">
                <span>Suggested Priority</span>
                <strong class="high-text">High</strong>
            </div>

            <p class="small-note">
                AI values are sample data for frontend demonstration.
            </p>
        </div>

    </div>

</div>

<?php include("../includes/footer.php"); ?>