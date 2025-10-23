# Software Requirements Specification (SRS)

Team Task Management System with Video Conferencing & Payment Integration

Project Name: Team-Task-Management-System
Version: 3.0
Date: October 23, 2025
Framework: Laravel 11.x (Dockerized)
Target Market: Kenyan Institutions, SACCOs, and Corporate Organizations

Prepared by: Development Team

Status: Draft

---

## 1. Executive summary

The Team Task Management System is a comprehensive, enterprise-grade collaboration platform designed specifically for Kenyan institutions, SACCOs, and corporate organizations. Built on Laravel and deployed via Docker containers, the system integrates task management, real-time video conferencing, internal chat, M-Pesa payments, fundraising campaigns, and SACCO-style group management for community problem-solving.

Key differentiators:
- Docker containerization for easy deployment
- Admin-controlled user verification system
- Department-based role hierarchy matching institutional structures
- Integrated WebRTC video conferencing (Teams/Zoom-like functionality)
- M-Pesa payment integration for Kenyan market
- SACCO-style group management for community initiatives

## 2. Purpose and scope

Purpose:
This document specifies software requirements for the Team Task Management System. It will be used by product owners, developers, QA, and operations to guide design, implementation, testing, and deployment.

Scope:
- Core modules: authentication & verification, department-based roles, task management, chat, WebRTC meetings, scheduling, group (SACCO) management, M-Pesa payments, fundraising, notifications, admin dashboards.
- Deployment: Dockerized services (php-fpm, nginx, MySQL, Redis, WebSocket server, Node for frontend builds).

## 3. Definitions, acronyms, abbreviations
- 2FA: Two-Factor Authentication
- STK: Sim Toolkit (M-Pesa STK Push)
- M-Pesa / Daraja: Safaricom payment API
- WebRTC: Web Real-Time Communication
- TURN/STUN: NAT traversal servers for WebRTC

## 4. System overview

Architecture summary:
- Laravel backend (API + views/Inertia)
- Vue 3 frontend (Inertia or SPA), Tailwind CSS
- Redis for caching and queues
- MySQL (or PostgreSQL) for primary persistence
- WebSocket server (Soketi or Pusher) for real-time messaging
- WebRTC signaling via WebSocket; media peer-to-peer with TURN fallback

Deployment targets:
- Local/dev: Docker Compose
- Production: Container registry + Kubernetes or managed VMs

## 5. Stakeholders
- Super Admin
- Department Admins, Managers, Team Leads, Members
- Group Treasurers / Secretaries
- DevOps / Support
- External providers: Safaricom, TURN/STUN provider, SMTP, SMS gateway

## 6. Functional requirements (high level)

Detailed functional requirements are grouped by modules below. Each FR includes acceptance criteria and notes for test coverage.

6.1 Authentication & User Management
- FR-1.1: Admin-only user creation (acceptance: non-admin cannot create user records via UI/API).
- FR-1.2: Admin supplies institutional email; domain validated against whitelist.
- FR-1.3: Generate secure registration tokens valid 48 hours.
- FR-1.4: Send invitation email with unique link.
- FR-1.5: User completes profile and awaits admin approval.
- FR-1.6: Admin must verify identity and approve/decline registration.
- FR-1.7: 2FA via SMS for admin users (configurable for other high-privileged roles).
- FR-1.8: Password policy: min 12 chars, uppercase, lowercase, number, symbol.
- FR-1.9: Audit trail for user lifecycle events.

6.2 Department-Based Role Management
- FR-2.x: Multiple departments, unlimited hierarchy levels, per-department role assignment, role inheritance, and policy enforcement via Gates & Policies.

6.3 Task Management
- FR-6.x: Create/assign tasks, subtasks, dependencies, timeline/Gantt with drag-and-drop adjustments, notifications on updates, time tracking, file attachments.

6.4 Real-time Chat
- FR-3.x: Private and group chats, file sharing (<= 25MB), read receipts, typing indicators, message history persisted, real-time with Laravel Echo and Redis-backed queues.

6.5 Video Conferencing (WebRTC)
- FR-4.x: WebRTC signaling via WebSocket, STUN/TURN, up to 50 participants initially (peer-to-peer); recording support to S3, host controls (mute/remove/lock), screen sharing.

6.6 Meeting Scheduler
- FR-5.x: Create meetings (recurring), email/SMS invites, RSVP, calendar integration (iCal export), reminders.

6.7 SACCO-Style Group Management
- FR-7.x: Groups with contributions, treasurer/secretary/admin roles, contribution tracking, expense approval workflows.

6.8 M-Pesa Integration
- FR-8.x: Daraja STK Push, callback handling, idempotency, B2C disbursements, C2B and Paybill support, SMS/email receipts, KES currency.

6.9 Fundraising & Campaigns
- FR-9.x: Create campaigns, public donor wall, anonymous donations option, campaign updates, expense reporting, transparent disbursement workflows.

6.10 Notifications
- FR-10.x: In-app, email, SMS, browser push; user preferences and quiet hours.

## 7. Non-functional requirements (NFRs)

Security:
- NFR-11.x: Password hashing (bcrypt), 2FA for admins, rate limiting, Sanctum for API auth, TLS for all services, encrypted environment credentials, logging for payment transactions.

Performance & Scalability:
- NFR-12.x: Page load < 2s on 3G, API < 200ms for simple queries, WebSocket < 100ms latency, design to support 1000+ concurrent users; chat throughput 10,000+ msgs/min with horizontal scaling.

Availability & Reliability:
- 99.9% uptime SLA for critical services, healthchecks for containers, monitoring/alerts via Prometheus/Alertmanager (or managed alternatives).

Maintainability:
- CI pipeline (linting, tests), code coverage thresholds, logging and metrics (Sentry, Prometheus), documented runbooks.

## 8. External interfaces

- Safaricom Daraja API (M-Pesa)
- TURN/STUN servers (coturn or managed)
- SMTP provider (SES/Mailgun)
- SMS gateway (Africa's Talking or other)
- S3-compatible object storage for recordings/uploads

## 9. Data model summary

- See core table list in the project guide. Key models: users, departments, roles, chats/messages, meetings, tasks, groups, campaigns, donations, payments, activity_log, webrtc_sessions.

## 10. Use cases & user flows

- Admin invites & approves a user
- Manager schedules a meeting and invites participants
- User contributes to a group via M-Pesa STK Push
- Team lead creates tasks and assigns to users

## 11. Acceptance criteria & testing

- Define acceptance criteria per FR (examples included in full project plan).
- Test strategy: unit tests (Pest/PHPUnit), feature tests, Dusk/Browser tests for major flows, API contract tests, load tests (k6), security scans.

## 12. Security & compliance

- Payment data encrypted in transit and at rest, idempotency tokens for payments, validate Daraja callbacks and IPs, store secrets in vaults for production.

## 13. Performance & scaling

- Horizontal scaling for app and websocket tiers, Redis for queues and caching, optional read-replicas for DB, SFU for large meetings if required.

## 14. Deployment & operations

- Local: Docker Compose with images built from repository.
- Production: push images to registry, deploy to Kubernetes or managed VM cluster, set env vars in secrets manager, configure monitoring and backups.

## 15. Risks & mitigations

- M-Pesa sandbox differences — mitigate by early Safaricom coordination.
- WebRTC scalability — mitigate by planning SFU/Media server fallback.

## 16. Project milestones (condensed)

- Phase 1–8 as detailed in project guide (foundation to deployment) with deliverables and tests.

## 17. Appendix

- Sample config files and API endpoints referenced in the project guide.

---

End of SRS (Draft). For approval or changes, update this document and sign off in the repository.
