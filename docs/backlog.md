# Initial Project Backlog (Epics & Stories)

This backlog is derived from the SRS and organized by epic and priority for Phase 1 and Phase 2.

## Epic: Foundation & Authentication (Phase 1)

- Story: Dockerize project and provide developer compose files (High)
- Story: Install Laravel scaffold and run initial migrations (High)
- Story: Implement Admin-only user invitation flow (High)
- Story: Registration token generation & expiry (High)
- Story: Admin verification dashboard (Medium)
- Story: Email templates for invites and confirmations (Medium)
- Story: 2FA for admin users (SMS) (Medium)
- Story: Audit trails for user lifecycle (Low)

## Epic: Tasks & Timeline (Phase 2)
- Story: Task CRUD API and UI (High)
- Story: Task assignment and notifications (High)
- Story: Subtasks and dependencies (Medium)
- Story: Timeline/Gantt view (Medium)
- Story: File attachments on tasks (Medium)

## Epic: Real-time Chat (Phase 3)
- Story: Private chat backend & WebSocket signaling (High)
- Story: Group chat creation & management (High)
- Story: Message persistence and pagination (High)
- Story: File upload support (Medium)

## Epic: Video Conferencing (Phase 4)
- Story: WebRTC signaling and basic 1:1 calls (High)
- Story: Group calls with TURN support (High)
- Story: Meeting recording to S3 (Medium)

## Epic: M-Pesa Integration (Phase 6)
- Story: Daraja STK Push integration (High)
- Story: Callback handlers and idempotency (High)
- Story: Payment records and reconciliation UI (Medium)

## Next actions
- Triage stories into sprints (Sprint 0: Foundation)
- Create initial tasks and assign owners
- Prepare CI pipeline for builds and tests
