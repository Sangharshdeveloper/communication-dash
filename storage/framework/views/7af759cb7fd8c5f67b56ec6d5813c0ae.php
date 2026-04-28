<?php $__env->startSection('title', 'User Management'); ?>

<?php $__env->startPush('styles'); ?>
    <style>
        /* Action buttons */
        .action-btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 11px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .15s;
            white-space: nowrap;
            font-family: inherit;
            line-height: 1;
        }

        .btn-green {
            background: #e8f5ee;
            color: #006B3C;
            border: 1px solid #bbf7d0
        }

        .btn-green:hover {
            background: #d1fae5;
            transform: translateY(-1px)
        }

        .btn-blue {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe
        }

        .btn-blue:hover {
            background: #dbeafe;
            transform: translateY(-1px)
        }

        .btn-amber {
            background: #fffbeb;
            color: #d97706;
            border: 1px solid #fde68a
        }

        .btn-amber:hover {
            background: #fef3c7;
            transform: translateY(-1px)
        }

        .btn-red {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca
        }

        .btn-red:hover {
            background: #fee2e2;
            transform: translateY(-1px)
        }

        .btn-gray {
            background: #f3f4f6;
            color: #374151;
            border: 1px solid #e5e7eb
        }

        .btn-gray:hover {
            background: #e5e7eb;
            transform: translateY(-1px)
        }

        .actions-cell {
            display: flex;
            gap: 5px;
            flex-wrap: wrap
        }

        /* Modal */
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1000;
            background: rgba(0, 0, 0, .5);
            backdrop-filter: blur(4px);
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .modal-backdrop.open {
            display: flex
        }

        .modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .2);
            animation: mIn .2s ease;
            overflow: hidden;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        @keyframes mIn {
            from {
                opacity: 0;
                transform: scale(.95)
            }

            to {
                opacity: 1;
                transform: none
            }
        }

        .modal-header {
            padding: 18px 22px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-shrink: 0;
        }

        .modal-header h3 {
            font-size: 16px;
            font-weight: 700;
            color: #111827
        }

        .modal-close {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: #f3f4f6;
            border: none;
            cursor: pointer;
            font-size: 14px;
            color: #6b7280;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all .15s;
        }

        .modal-close:hover {
            background: #e5e7eb;
            color: #111827
        }

        .modal-body {
            padding: 20px 22px;
            overflow-y: auto
        }

        .modal-footer {
            padding: 14px 22px;
            border-top: 1px solid #f3f4f6;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            flex-shrink: 0;
        }

        /* Form */
        .fg {
            margin-bottom: 14px
        }

        .fg-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px
        }

        .fg label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px
        }

        .fg input,
        .fg select {
            width: 100%;
            padding: 9px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            color: #111827;
            font-family: inherit;
            background: #fff;
            transition: all .15s;
        }

        .fg input:focus,
        .fg select:focus {
            outline: none;
            border-color: #006B3C;
            box-shadow: 0 0 0 3px rgba(0, 107, 60, .1);
        }

        .fg .hint {
            font-size: 11px;
            color: #9ca3af;
            margin-top: 4px
        }

        .fg .err-msg {
            font-size: 12px;
            color: #dc2626;
            margin-top: 4px
        }

        .fg input.invalid,
        .fg select.invalid {
            border-color: #dc2626
        }

        .status-switch {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            padding: 8px 0;
        }

        .status-switch input[type=checkbox] {
            width: 16px;
            height: 16px;
            accent-color: #006B3C;
            cursor: pointer
        }

        /* Link box */
        .link-box {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            padding: 12px 14px;
            font-size: 13px;
            word-break: break-all;
            color: #374151;
            font-family: monospace;
            line-height: 1.55;
            margin: 12px 0;
        }

        .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: #4b5563;
            margin: 6px 0
        }

        .info-row strong {
            color: #111827;
            min-width: 80px
        }

        .alert-info {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #1e40af;
            margin: 10px 0;
        }

        .alert-amber {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #92400e;
            margin: 10px 0;
        }

        .alert-red {
            background: #fef2f2;
            border: 1px solid #fecaca;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #991b1b;
            margin: 10px 0;
        }

        .alert-green {
            background: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 12px;
            color: #065f46;
            margin: 10px 0;
        }

        .spin-sm {
            width: 20px;
            height: 20px;
            border: 2px solid #e5e7eb;
            border-top-color: #006B3C;
            border-radius: 50%;
            animation: spin .7s linear infinite;
            display: inline-block;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg)
            }
        }

        /* Session list */
        .session-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 10px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            margin-bottom: 8px;
            transition: border-color .15s;
        }

        .session-item:hover {
            border-color: #006B3C
        }

        .sess-av {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #006B3C;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700;
            font-size: 13px;
            flex-shrink: 0;
        }

        .sess-info {
            flex: 1;
            min-width: 0
        }

        .sess-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827
        }

        .sess-meta {
            font-size: 11px;
            color: #6b7280;
            margin-top: 2px
        }

        .open-chat-btn {
            font-size: 11px;
            color: #006B3C;
            font-weight: 700;
            text-decoration: none;
            padding: 4px 10px;
            background: #e8f5ee;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            white-space: nowrap;
        }

        /* Toast */
        #app-toast {
            position: fixed;
            bottom: 24px;
            left: 50%;
            transform: translateX(-50%) translateY(20px);
            background: #111827;
            color: #fff;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 13px;
            opacity: 0;
            transition: all .25s;
            z-index: 9999;
            white-space: nowrap;
            pointer-events: none;
            max-width: 90vw;
        }

        #app-toast.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0)
        }

        #app-toast.success {
            background: #065f46
        }

        #app-toast.error {
            background: #991b1b
        }

        .add-user-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #006B3C, #00994d);
            color: #fff;
            padding: 9px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(0, 107, 60, .25);
        }

        .add-user-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 107, 60, .35)
        }

        /* Add inside existing <style> block */

        .pwd-wrap {
            position: relative;
        }

        .pwd-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #9ca3af;
            font-size: 14px;
            background: none;
            border: none;
            padding: 2px;
            transition: color .14s;
        }

        .pwd-toggle:hover {
            color: #374151;
        }

        .pwd-strength {
            display: flex;
            gap: 3px;
            margin-top: 6px;
        }

        .pwd-strength-bar {
            height: 3px;
            flex: 1;
            border-radius: 2px;
            background: #e5e7eb;
            transition: background .2s;
        }

        .pwd-strength-bar.weak {
            background: #ef4444;
        }

        .pwd-strength-bar.medium {
            background: #f59e0b;
        }

        .pwd-strength-bar.strong {
            background: #10b981;
        }

        .pwd-match-msg {
            font-size: 11px;
            margin-top: 4px;
        }

        .pwd-match-msg.ok {
            color: #059669;
        }

        .pwd-match-msg.bad {
            color: #dc2626;
        }

        .field-divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin: 16px 0 14px;
        }

        .section-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            text-transform: uppercase;
            letter-spacing: .06em;
            margin-bottom: 12px;
            margin-top: 2px;
        }

        .bitrix-field {
            display: none;
        }

        .bitrix-field.visible {
            display: block;
        }

        /* Fix SVG size forcefully */
        .pagination svg {
            width: 12px !important;
            height: 12px !important;
        }

        /* Fix button size (THIS is main issue) */
        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 28px;
            height: 28px;
            padding: 0 6px;
            font-size: 12px;
            line-height: 1;
        }

        /* Prevent stretching */
        .pagination li {
            display: inline-flex;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 6px;
            align-items: center;
        }

        .pagination li a,
        .pagination li span {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 5px 8px;
            font-size: 12px;
            border-radius: 6px;
        }
    </style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <div class="page-container">

        <div class="page-header"
            style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:10px;">
            <div>
                <h1 class="page-title">👥 User Management</h1>
                <p class="page-subtitle">Create, edit and manage platform users</p>
            </div>
            <button class="add-user-btn" onclick="openUserModal()">
                <span style="font-size:17px;line-height:1">+</span>
                <span>Add New User</span>
            </button>
        </div>

        
        <div class="card" style="margin-bottom:20px;">
            <div class="card-body">
                <form method="GET" style="display:flex;gap:12px;align-items:flex-end;flex-wrap:wrap;">
                    <div style="flex:2;min-width:200px;">
                        <label class="form-label">Search</label>
                        <input type="text" name="search" class="form-control" placeholder="Name or email..."
                            value="<?php echo e(request('search')); ?>">
                    </div>
                    <div style="flex:1;min-width:140px;">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-control">
                            <option value="">All Roles</option>
                            <?php $__currentLoopData = ['admin', 'agent', 'auditor', 'customer']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role); ?>" <?php if(request('role') === $role): echo 'selected'; endif; ?>><?php echo e(ucfirst($role)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary">Search</button>
                        <a href="<?php echo e(route('admin.users')); ?>" class="btn btn-outline" style="margin-left:8px;">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        
        <div class="card">
            <div class="card-header"><?php echo e($users->total()); ?> users</div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Last Login</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div style="font-weight:500"><?php echo e($user->name); ?></div>
                                    <div class="text-muted text-sm"><?php echo e($user->email); ?></div>
                                </td>
                                <td>
                                    <span
                                        class="badge
                <?php if($user->role === 'admin'): ?> badge-danger
                <?php elseif($user->role === 'agent'): ?> badge-info
                <?php elseif($user->role === 'auditor'): ?> badge-warning
                <?php else: ?> badge-gray <?php endif; ?>">
                                        <?php echo e(ucfirst($user->role)); ?>

                                    </span>
                                </td>
                                <td>
                                    <?php if($user->is_active): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-sm text-muted">
                                    <?php echo e($user->last_login_at?->setTimezone('Asia/Dubai')->format('d M Y H:i') ?? '—'); ?>

                                </td>
                                <td class="text-sm text-muted"><?php echo e($user->created_at->format('d M Y')); ?></td>
                                <td>
                                    <div class="actions-cell">
                                        <button class="action-btn btn-gray" onclick="openUserModal(<?php echo e($user->id); ?>)"
                                            title="Edit">✎ Edit</button>

                                        <?php if($user->role === 'customer'): ?>
                                            <button class="action-btn btn-blue"
                                                onclick="openMagicLinkModal(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>', '<?php echo e($user->email); ?>')"
                                                title="Send login link">
                                                🔗 Login Link
                                            </button>
                                            <button class="action-btn btn-green"
                                                onclick="openChatLinkModal(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>', '<?php echo e($user->email); ?>')"
                                                title="Send chat link">
                                                💬 Chat Link
                                            </button>
                                        <?php elseif($user->role === 'agent'): ?>
                                            <button class="action-btn btn-blue"
                                                onclick="openAgentSessionsModal(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>')">
                                                💬 View Chats
                                            </button>
                                        <?php endif; ?>

                                        <?php if($user->id !== auth()->id()): ?>
                                            <button class="action-btn btn-red"
                                                onclick="confirmDelete(<?php echo e($user->id); ?>, '<?php echo e(addslashes($user->name)); ?>')"
                                                title="Delete">🗑</button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" style="text-align:center;padding:40px;color:#9ca3af;">
                                    No users found. Click "Add New User" to get started.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <?php if($users->hasPages()): ?>
                <div style="padding:14px 20px;border-top:1px solid #f3f4f6;text-align:center;">
                    <?php echo e($users->links('pagination::simple-bootstrap-5')); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>

    
    
    <div class="modal-backdrop" id="user-modal" onclick="closeModal('user-modal')">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3 id="user-modal-title">Add New User</h3>
                <button class="modal-close" onclick="closeModal('user-modal')">✕</button>
            </div>
            <div class="modal-body">
                <form id="user-form" onsubmit="event.preventDefault(); submitUserForm();">
                    <input type="hidden" id="user-id">

                    
                    <p class="section-label">Basic Information</p>

                    <div class="fg-row">
                        <div class="fg">
                            <label>Full Name *</label>
                            <input type="text" id="user-name" required placeholder="John Doe">
                            <div class="err-msg" id="err-name"></div>
                        </div>
                        <div class="fg">
                            <label>Phone</label>
                            <input type="text" id="user-phone" placeholder="+971 50 123 4567">
                        </div>
                    </div>

                    <div class="fg">
                        <label>Email Address *</label>
                        <input type="email" id="user-email" required placeholder="user@domain.ae">
                        <div class="err-msg" id="err-email"></div>
                    </div>

                    <div class="fg-row">
                        <div class="fg">
                            <label>Role *</label>
                            <select id="user-role" required onchange="onRoleChange()">
                                <option value="customer">Customer</option>
                                <option value="agent">Agent</option>
                                <option value="auditor">Auditor</option>
                                <option value="admin">Admin</option>
                            </select>
                            <div class="err-msg" id="err-role"></div>
                        </div>
                        <div class="fg">
                            <label class="status-switch" style="margin-top:20px">
                                <input type="checkbox" id="user-active" checked>
                                <span>Account is active</span>
                            </label>
                        </div>
                    </div>

                    
                    <div class="fg bitrix-field" id="bitrix-field-wrap">
                        <hr class="field-divider">
                        <p class="section-label">Agent Settings</p>
                        <label>Bitrix Agent ID</label>
                        <input type="text" id="user-bitrix-agent-id" placeholder="e.g. 42 or USER_123">
                        <div class="hint">Used to link this agent to their Bitrix24 profile.</div>
                    </div>

                    
                    <div id="password-section" style="display:none">
                        <hr class="field-divider">
                        <p class="section-label" id="pwd-section-label">Set Password</p>

                        <div class="fg">
                            <label>Password <span id="pwd-required-star">*</span></label>
                            <div class="pwd-wrap">
                                <input type="password" id="user-password" minlength="8" autocomplete="new-password"
                                    placeholder="Min 8 characters" oninput="onPwdInput()">
                                <button type="button" class="pwd-toggle"
                                    onclick="togglePwd('user-password', this)">👁</button>
                            </div>
                            <div class="pwd-strength" id="pwd-strength-bars">
                                <div class="pwd-strength-bar" id="psb1"></div>
                                <div class="pwd-strength-bar" id="psb2"></div>
                                <div class="pwd-strength-bar" id="psb3"></div>
                                <div class="pwd-strength-bar" id="psb4"></div>
                            </div>
                            <div class="hint" id="password-hint">Min 8 characters.</div>
                            <div class="err-msg" id="err-password"></div>
                        </div>

                        <div class="fg">
                            <label>Confirm Password <span id="pwd-confirm-star">*</span></label>
                            <div class="pwd-wrap">
                                <input type="password" id="user-password-confirm" minlength="8"
                                    autocomplete="new-password" placeholder="Re-enter password"
                                    oninput="onPwdConfirmInput()">
                                <button type="button" class="pwd-toggle"
                                    onclick="togglePwd('user-password-confirm', this)">👁</button>
                            </div>
                            <div class="pwd-match-msg" id="pwd-match-msg"></div>
                        </div>
                    </div>

                    <div class="alert-info" id="role-info" style="margin-top:8px">
                        ℹ️ Customer accounts log in via magic link (no password needed).
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('user-modal')">Cancel</button>
                <button class="btn btn-primary" id="user-submit-btn" onclick="submitUserForm()">
                    <span id="user-submit-text">Create User</span>
                </button>
            </div>
        </div>
    </div>

    
    <div class="modal-backdrop" id="magic-link-modal" onclick="closeModal('magic-link-modal')">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>🔗 Send Login Link</h3>
                <button class="modal-close" onclick="closeModal('magic-link-modal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="info-row"><strong>Customer:</strong> <span id="ml-customer-name">—</span></div>
                <div class="info-row"><strong>Email:</strong> <span id="ml-customer-email">—</span></div>

                <div class="alert-info" style="margin-top:14px">
                    📧 A secure one-time login link will be emailed to this customer.
                    The link expires in <?php echo e(config('magic_link.expiry_minutes', 10)); ?> minutes.
                </div>

                <div id="ml-result" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('magic-link-modal')">Cancel</button>
                <button class="btn btn-primary" onclick="sendMagicLink()" id="ml-send-btn">
                    <span id="ml-send-text">📧 Send Login Link</span>
                </button>
            </div>
        </div>
    </div>

    
    <div class="modal-backdrop" id="chat-link-modal" onclick="closeModal('chat-link-modal')">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>💬 Send Chat Link</h3>
                <button class="modal-close" onclick="closeModal('chat-link-modal')">✕</button>
            </div>
            <div class="modal-body">
                <div class="info-row"><strong>Customer:</strong> <span id="cl-customer-name">—</span></div>
                <div class="info-row"><strong>Email:</strong> <span id="cl-customer-email">—</span></div>

                <div class="fg" style="margin-top:14px">
                    <label>Assign to Agent *</label>
                    <select id="cl-agent-select" onchange="refreshChatLinkPreview()">
                        <option value="">— Select an agent —</option>
                        <?php $__currentLoopData = $allAgents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($agent->id); ?>"><?php echo e($agent->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div id="cl-preview" style="display:none">
                    <div style="font-size:12px;color:#6b7280;margin-bottom:4px;font-weight:600">Link preview:</div>
                    <div class="link-box" id="cl-link-box">—</div>
                </div>

                <div class="alert-amber">
                    ⚠️ Only the assigned agent and the customer can use this chat session.
                </div>

                <div id="cl-result" style="display:none"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('chat-link-modal')">Cancel</button>
                <button class="btn btn-outline" onclick="copyChatLink()" id="cl-copy-btn" style="display:none">📋
                    Copy</button>
                <button class="btn btn-primary" onclick="sendChatLink()" id="cl-send-btn" disabled>
                    <span id="cl-send-text">📧 Send via Email</span>
                </button>
            </div>
        </div>
    </div>

    
    <div class="modal-backdrop" id="sessions-modal" onclick="closeModal('sessions-modal')">
        <div class="modal" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>💬 <span id="ss-agent-name">Agent</span>'s Conversations</h3>
                <button class="modal-close" onclick="closeModal('sessions-modal')">✕</button>
            </div>
            <div class="modal-body">
                <div id="ss-loading" style="text-align:center;padding:30px">
                    <div class="spin-sm"></div>
                </div>
                <div id="ss-list"></div>
                <div id="ss-empty" style="display:none;text-align:center;padding:30px;color:#9ca3af">
                    <div style="font-size:40px;margin-bottom:8px">💬</div>
                    <p>No conversations yet.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('sessions-modal')">Close</button>
            </div>
        </div>
    </div>

    
    <div class="modal-backdrop" id="delete-modal" onclick="closeModal('delete-modal')">
        <div class="modal" style="max-width:420px" onclick="event.stopPropagation()">
            <div class="modal-header">
                <h3>🗑 Delete User</h3>
                <button class="modal-close" onclick="closeModal('delete-modal')">✕</button>
            </div>
            <div class="modal-body">
                <p style="font-size:14px;color:#374151;margin-bottom:8px">
                    Are you sure you want to delete <strong id="del-name">—</strong>?
                </p>
                <div class="alert-red">
                    ⚠️ The user will be soft-deleted. They will lose access immediately.
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline" onclick="closeModal('delete-modal')">Cancel</button>
                <button class="btn btn-primary" style="background:#dc2626" onclick="executeDelete()" id="del-btn">
                    <span id="del-btn-text">Delete User</span>
                </button>
            </div>
        </div>
    </div>

    <div id="app-toast"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        const CSRF = '<?php echo e(csrf_token()); ?>';
        const URLS = {
            users: '<?php echo e(route('admin.users.store')); ?>', // POST create
            user: '/admin/users', // /{id} PUT/DELETE/GET
            chatLink: '<?php echo e(route('admin.users.chat-link')); ?>',
            sendMagic: (id) => `/admin/users/${id}/send-magic-link`,
            sendChat: (id) => `/admin/users/${id}/send-chat-link`,
            agentSess: (id) => `/admin/users/${id}/agent-sessions`,
        };

        // Active state
        let activeUserId = null;
        let activeCustomerId = null;
        let activeCustomerName = '';
        let activeCustomerEmail = '';
        let activeAgentId = null;
        let activeDeleteId = null;
        let currentChatUrl = '';

        // ── Modal helpers ────────────────────────────────────────────────────────────
        function openModal(id) {
            document.getElementById(id).classList.add('open');
        }

        function closeModal(id) {
            document.getElementById(id).classList.remove('open');
        }

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                ['user-modal', 'magic-link-modal', 'chat-link-modal', 'sessions-modal', 'delete-modal']
                .forEach(closeModal);
            }
        });

        // ── Toast ────────────────────────────────────────────────────────────────────
        function toast(msg, type = 'info') {
            const t = document.getElementById('app-toast');
            t.textContent = msg;
            t.className = 'show ' + type;
            clearTimeout(window._tt);
            window._tt = setTimeout(() => t.className = '', 3000);
        }

        // ── USER CRUD ────────────────────────────────────────────────────────────────
        function openUserModal(userId = null) {
            activeUserId = userId;
            clearErrors();

            // Reset all fields
            ['user-id', 'user-name', 'user-email', 'user-phone', 'user-password', 'user-password-confirm',
                'user-bitrix-agent-id'
            ]
            .forEach(id => {
                const el = document.getElementById(id);
                if (el) el.value = '';
            });
            document.getElementById('user-active').checked = true;
            document.getElementById('user-role').value = 'customer';
            document.getElementById('pwd-match-msg').textContent = '';
            resetStrengthBars();

            if (userId) {
                document.getElementById('user-modal-title').textContent = 'Edit User';
                document.getElementById('user-submit-text').textContent = 'Save Changes';
                openModal('user-modal');

                fetch(`${URLS.user}/${userId}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(u => {
                        document.getElementById('user-id').value = u.id;
                        document.getElementById('user-name').value = u.name;
                        document.getElementById('user-email').value = u.email;
                        document.getElementById('user-phone').value = u.phone || '';
                        document.getElementById('user-role').value = u.role;
                        document.getElementById('user-active').checked = u.is_active;
                        document.getElementById('user-bitrix-agent-id').value = u.bitrix_agent_id || '';
                        onRoleChange();
                    })
                    .catch(() => toast('Failed to load user data', 'error'));
            } else {
                document.getElementById('user-modal-title').textContent = 'Add New User';
                document.getElementById('user-submit-text').textContent = 'Create User';
                onRoleChange();
                openModal('user-modal');
            }
        }

        function onRoleChange() {
            const role = document.getElementById('user-role').value;
            const pwdSec = document.getElementById('password-section');
            const pwdStar = document.getElementById('pwd-required-star');
            const pwdCStar = document.getElementById('pwd-confirm-star');
            const pwdHint = document.getElementById('password-hint');
            const pwdLabel = document.getElementById('pwd-section-label');
            const roleInfo = document.getElementById('role-info');
            const bitrix = document.getElementById('bitrix-field-wrap');

            // Bitrix field — agents only
            bitrix.classList.toggle('visible', role === 'agent');

            if (role === 'customer') {
                pwdSec.style.display = 'none';
                roleInfo.innerHTML = 'ℹ️ Customer accounts log in via magic link — no password needed.';
                roleInfo.className = 'alert-info';
            } else {
                pwdSec.style.display = 'block';
                const isEdit = !!activeUserId;
                pwdLabel.textContent = isEdit ? 'Change Password' : 'Set Password';
                pwdHint.textContent = isEdit ?
                    'Leave blank to keep the current password.' :
                    'Required for staff accounts. Min 8 characters.';
                // Stars — required only on create
                pwdStar.style.display = isEdit ? 'none' : 'inline';
                pwdCStar.style.display = isEdit ? 'none' : 'inline';
                roleInfo.innerHTML =
                    `ℹ️ <strong>${role.charAt(0).toUpperCase()+role.slice(1)}</strong> accounts sign in with email + password.`;
                roleInfo.className = 'alert-info';
            }
        }

        // ── Password helpers ─────────────────────────────────────────────────────────
        function togglePwd(inputId, btn) {
            const inp = document.getElementById(inputId);
            const isText = inp.type === 'text';
            inp.type = isText ? 'password' : 'text';
            btn.textContent = isText ? '👁' : '🙈';
        }

        function pwdStrength(pwd) {
            let score = 0;
            if (pwd.length >= 8) score++;
            if (pwd.length >= 12) score++;
            if (/[A-Z]/.test(pwd) && /[0-9]/.test(pwd)) score++;
            if (/[^A-Za-z0-9]/.test(pwd)) score++;
            return score; // 0–4
        }

        function resetStrengthBars() {
            [1, 2, 3, 4].forEach(i => {
                const b = document.getElementById('psb' + i);
                if (b) {
                    b.className = 'pwd-strength-bar';
                }
            });
        }

        function onPwdInput() {
            const pwd = document.getElementById('user-password').value;
            const score = pwdStrength(pwd);
            const level = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
            [1, 2, 3, 4].forEach(i => {
                const b = document.getElementById('psb' + i);
                b.className = 'pwd-strength-bar' + (i <= score ? ' ' + level : '');
            });
            onPwdConfirmInput(); // re-check match
        }

        function onPwdConfirmInput() {
            const pwd = document.getElementById('user-password').value;
            const conf = document.getElementById('user-password-confirm').value;
            const msg = document.getElementById('pwd-match-msg');
            if (!conf) {
                msg.textContent = '';
                return;
            }
            if (pwd === conf) {
                msg.textContent = '✓ Passwords match';
                msg.className = 'pwd-match-msg ok';
            } else {
                msg.textContent = '✗ Passwords do not match';
                msg.className = 'pwd-match-msg bad';
            }
        }
        async function submitUserForm() {
            clearErrors();

            const role = document.getElementById('user-role').value;
            const pwd = document.getElementById('user-password').value;
            const conf = document.getElementById('user-password-confirm').value;

            // Client-side password validation for staff
            if (role !== 'customer') {
                if (!activeUserId && !pwd) {
                    document.getElementById('err-password').textContent =
                    'Password is required for new staff accounts.';
                    document.getElementById('user-password').classList.add('invalid');
                    return;
                }
                if (pwd && pwd !== conf) {
                    document.getElementById('pwd-match-msg').textContent = '✗ Passwords do not match';
                    document.getElementById('pwd-match-msg').className = 'pwd-match-msg bad';
                    document.getElementById('user-password-confirm').classList.add('invalid');
                    return;
                }
                if (pwd && pwd.length < 8) {
                    document.getElementById('err-password').textContent = 'Password must be at least 8 characters.';
                    document.getElementById('user-password').classList.add('invalid');
                    return;
                }
            }

            const payload = {
                name: document.getElementById('user-name').value.trim(),
                email: document.getElementById('user-email').value.trim(),
                phone: document.getElementById('user-phone').value.trim(),
                role: role,
                password: pwd || null,
                is_active: document.getElementById('user-active').checked,
                bitrix_agent_id: role === 'agent' ?
                    (document.getElementById('user-bitrix-agent-id').value.trim() || null) :
                    null,
            };

            const isEdit = !!activeUserId;
            const url = isEdit ? `${URLS.user}/${activeUserId}` : URLS.users;
            const method = isEdit ? 'PUT' : 'POST';

            document.getElementById('user-submit-btn').disabled = true;
            document.getElementById('user-submit-text').textContent = isEdit ? 'Saving...' : 'Creating...';

            try {
                const res = await fetch(url, {
                    method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });
                const data = await res.json();

                if (res.status === 422) {
                    showErrors(data.errors || {});
                    throw new Error('Validation failed');
                }
                if (!res.ok) throw new Error(data.message || 'Save failed');

                toast(data.message || 'Saved!', 'success');
                closeModal('user-modal');
                setTimeout(() => location.reload(), 500);
            } catch (e) {
                if (e.message !== 'Validation failed') toast(e.message, 'error');
            } finally {
                document.getElementById('user-submit-btn').disabled = false;
                document.getElementById('user-submit-text').textContent = isEdit ? 'Save Changes' : 'Create User';
            }
        }

        function showErrors(errors) {
            Object.entries(errors).forEach(([field, msgs]) => {
                const err = document.getElementById('err-' + field);
                const inp = document.getElementById('user-' + field);
                if (err) err.textContent = Array.isArray(msgs) ? msgs[0] : msgs;
                if (inp) inp.classList.add('invalid');
            });
        }

        function clearErrors() {
            ['name', 'email', 'role', 'password'].forEach(f => {
                const err = document.getElementById('err-' + f);
                const inp = document.getElementById('user-' + f);
                if (err) err.textContent = '';
                if (inp) inp.classList.remove('invalid');
            });
            const conf = document.getElementById('user-password-confirm');
            if (conf) conf.classList.remove('invalid');
            const matchMsg = document.getElementById('pwd-match-msg');
            if (matchMsg) matchMsg.textContent = '';
            resetStrengthBars();
        }

        // ── DELETE ───────────────────────────────────────────────────────────────────
        function confirmDelete(id, name) {
            activeDeleteId = id;
            document.getElementById('del-name').textContent = name;
            openModal('delete-modal');
        }

        async function executeDelete() {
            if (!activeDeleteId) return;
            document.getElementById('del-btn').disabled = true;
            document.getElementById('del-btn-text').textContent = 'Deleting...';
            try {
                const res = await fetch(`${URLS.user}/${activeDeleteId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message || 'Delete failed');
                toast(data.message, 'success');
                closeModal('delete-modal');
                setTimeout(() => location.reload(), 500);
            } catch (e) {
                toast(e.message, 'error');
            } finally {
                document.getElementById('del-btn').disabled = false;
                document.getElementById('del-btn-text').textContent = 'Delete User';
            }
        }

        // ── SEND MAGIC LOGIN LINK ────────────────────────────────────────────────────
        function openMagicLinkModal(userId, name, email) {
            activeCustomerId = userId;
            activeCustomerName = name;
            activeCustomerEmail = email;
            document.getElementById('ml-customer-name').textContent = name;
            document.getElementById('ml-customer-email').textContent = email;
            document.getElementById('ml-result').style.display = 'none';
            document.getElementById('ml-send-btn').disabled = false;
            document.getElementById('ml-send-text').textContent = '📧 Send Login Link';
            openModal('magic-link-modal');
        }

        async function sendMagicLink() {
            if (!activeCustomerId) return;
            const btn = document.getElementById('ml-send-btn');
            const btnT = document.getElementById('ml-send-text');
            const result = document.getElementById('ml-result');

            btn.disabled = true;
            btnT.textContent = 'Sending...';

            try {
                const res = await fetch(URLS.sendMagic(activeCustomerId), {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json'
                    },
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                result.className = 'alert-green';
                result.innerHTML = '✅ ' + data.message;
                result.style.display = 'block';
                btnT.textContent = '✓ Sent!';
                toast(data.message, 'success');
                setTimeout(() => closeModal('magic-link-modal'), 2000);
            } catch (e) {
                result.className = 'alert-red';
                result.innerHTML = '❌ ' + e.message;
                result.style.display = 'block';
                btn.disabled = false;
                btnT.textContent = '📧 Send Login Link';
            }
        }

        // ── SEND CHAT LINK ───────────────────────────────────────────────────────────
        function openChatLinkModal(userId, name, email) {
            activeCustomerId = userId;
            activeCustomerName = name;
            activeCustomerEmail = email;
            activeAgentId = null;
            currentChatUrl = '';

            document.getElementById('cl-customer-name').textContent = name;
            document.getElementById('cl-customer-email').textContent = email;
            document.getElementById('cl-agent-select').value = '';
            document.getElementById('cl-preview').style.display = 'none';
            document.getElementById('cl-result').style.display = 'none';
            document.getElementById('cl-send-btn').disabled = true;
            document.getElementById('cl-copy-btn').style.display = 'none';
            document.getElementById('cl-send-text').textContent = '📧 Send via Email';

            openModal('chat-link-modal');
        }

        async function refreshChatLinkPreview() {
            const sel = document.getElementById('cl-agent-select');
            activeAgentId = sel.value ? parseInt(sel.value) : null;
            if (!activeAgentId) {
                document.getElementById('cl-preview').style.display = 'none';
                document.getElementById('cl-send-btn').disabled = true;
                document.getElementById('cl-copy-btn').style.display = 'none';
                return;
            }

            try {
                const res = await fetch(URLS.chatLink, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        customer_id: activeCustomerId,
                        agent_id: activeAgentId
                    }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                currentChatUrl = data.url;
                document.getElementById('cl-link-box').textContent = data.url;
                document.getElementById('cl-preview').style.display = 'block';
                document.getElementById('cl-send-btn').disabled = false;
                document.getElementById('cl-copy-btn').style.display = 'inline-flex';
            } catch (e) {
                toast(e.message, 'error');
            }
        }

        function copyChatLink() {
            if (!currentChatUrl) return;
            navigator.clipboard.writeText(currentChatUrl).then(() => toast('Link copied!', 'success'));
        }

        async function sendChatLink() {
            if (!activeCustomerId || !activeAgentId) return;

            const btn = document.getElementById('cl-send-btn');
            const btnT = document.getElementById('cl-send-text');
            const result = document.getElementById('cl-result');

            btn.disabled = true;
            btnT.textContent = 'Sending...';

            try {
                const res = await fetch(URLS.sendChat(activeCustomerId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        agent_id: activeAgentId
                    }),
                });
                const data = await res.json();
                if (!res.ok) throw new Error(data.message);

                result.className = 'alert-green';
                result.innerHTML = '✅ ' + data.message;
                result.style.display = 'block';
                btnT.textContent = '✓ Sent!';
                toast(data.message, 'success');
                setTimeout(() => closeModal('chat-link-modal'), 2000);
            } catch (e) {
                result.className = 'alert-red';
                result.innerHTML = '❌ ' + e.message;
                result.style.display = 'block';
                btn.disabled = false;
                btnT.textContent = '📧 Send via Email';
            }
        }

        // ── AGENT SESSIONS ───────────────────────────────────────────────────────────
        async function openAgentSessionsModal(agentId, agentName) {
            document.getElementById('ss-agent-name').textContent = agentName;
            document.getElementById('ss-loading').style.display = 'block';
            document.getElementById('ss-list').innerHTML = '';
            document.getElementById('ss-empty').style.display = 'none';
            openModal('sessions-modal');

            try {
                const res = await fetch(URLS.agentSess(agentId), {
                    headers: {
                        'Accept': 'application/json'
                    }
                });
                const sessions = await res.json();

                document.getElementById('ss-loading').style.display = 'none';

                if (!sessions.length) {
                    document.getElementById('ss-empty').style.display = 'block';
                    return;
                }

                const list = document.getElementById('ss-list');
                list.innerHTML = sessions.map(s => `
      <div class="session-item">
        <div class="sess-av">${esc(s.customer_name.charAt(0).toUpperCase())}</div>
        <div class="sess-info">
          <div class="sess-name">${esc(s.customer_name)}</div>
          <div class="sess-meta">
            ${s.customer_ref ? 'ID: ' + esc(s.customer_ref) + ' · ' : ''}
            ${s.messages_count} msgs · ${esc(s.last_activity)}
          </div>
        </div>
        <a href="${esc(s.view_url)}" target="_blank" class="open-chat-btn">Open ↗</a>
      </div>`).join('');
            } catch (e) {
                document.getElementById('ss-loading').style.display = 'none';
                document.getElementById('ss-empty').style.display = 'block';
                document.getElementById('ss-empty').innerHTML = '<p>Failed to load.</p>';
            }
        }

        function esc(s) {
            return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        }
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/sangharshsulke/Axis Data/communication-dash/resources/views/admin/users.blade.php ENDPATH**/ ?>