# Senior Project Defense: Forex Trading Journal and Analyst Feedback System

## Slide 1: Title Slide
*   **Project Title**: Forex Trading Journal with Performance Analytics & Analyst Marketplace
*   **Student Names**: Daniel Eshetu, Natnael Feseha, Tigistu Begashaw, Yoseph Wegayehu
*   **Advisor**: Metages Ephrem
*   **Institution**: St. Mary's University, Faculty of Informatics
*   **Date**: June 2025

**Speaker Notes**:
"Good morning/afternoon. We are the development team behind the Forex Trading Journal and Analyst Feedback System. Our project addresses the critical need for disciplined trading and professional mentorship in the retail forex market."

---

## Slide 2: Introduction
*   **Background**: Retail Forex trading is a $7 trillion/day market, but 70-80% of retail traders fail due to lack of discipline and feedback.
*   **Problem**:
    *   Manual journaling (spreadsheets) is tedious and lacks analytics.
    *   Professional coaching is expensive ($200+/hr) and inaccessible.
    *   Gap between trade execution and performance review.
*   **Motivation**: To democratize access to institutional-grade analytics and professional mentorship for individual traders using web technology.

**Speaker Notes**:
"The core problem we identified is that while the market is accessible, success is not. Most traders drift without recognizing their mistakes. Existing tools are either too simple (spreadsheets) or too expensive (institutional tools). Our system bridges this gap."

---

## Slide 3: Objectives of the System
*   **General Objective**: Develop a unified web application that combines performance tracking with a marketplace for professional analyst feedback.
*   **Specific Objectives**:
    *   Implement **Trade Logging** with manual form and bulk CSV import.
    *   Create an **Analytics Dashboard** for visualizing metrics (Win Rate, Drawdown).
    *   Establish a **Verified Analyst Marketplace** with subscription tiers.
    *   Integrate **Chapa Payment Gateway** for local payments.
    *   Provide **Role-Based Access Control** (Trader, Analyst, Admin).

**Speaker Notes**:
"Our main goal was integration. We didn't just want a journal, and we didn't just want a coaching site. We wanted them together. Specific goals included handling data import, visualizing that data, and monetizing analyst expertise."

---

## Slide 4: Scope of the System
*   **Trader Scope**:
    *   Log/Import trades.
    *   View Analytics (Charts/Graphs).
    *   Subscribe to Analysts.
*   **Analyst Scope**:
    *   Create Subscription Tiers (Basic, Premium, Elite).
    *   Review Subscriber Data.
    *   Provide Feedback (Manual & AI-Assisted).
*   **Admin Scope**:
    *   Verify Analysts.
    *   Resolve Disputes.
    *   System Monitoring.
*   **Limitations**: No live trade execution (Broker API integration is future work).

**Speaker Notes**:
"We scoped the system to three distinct roles. Detailed analytics and hiring coaches are the core for Traders. Analysts get tools to manage their business. Crucially, we do *not* execute trades; this is a post-trade analysis tool."

---

## Slide 5: System Actors
*   **Trader**: The primary end-user seeking to improve performance through data and coaching.
*   **Performance Analyst**: Verified professional providing feedback and monetization of their expertise (70% revenue share).
*   **Administrator**: System overseer responsible for quality control, verifications, and platform health.

**Speaker Notes**:
"The Trader is the consumer. The Analyst is the service provider. The Admin is the regulator. This triangular relationship ensures quality and trust in the marketplace."

---

## Slide 6: System Architecture
*   **High-Level**: Web-based MVC Architecture.
*   **Tech Stack**: Laravel 12.0 (Backend), TailwindCSS (Frontend), SQLite/MySQL (Database).
*   **Service Layer Pattern**:
    *   `SubscriptionService` (Handles billing logic).
    *   `AiCoachingService` (Gemini AI integration).
    *   `ChapaPaymentService` (Payment adapter).
*   **Diagram**: [Insert Figure 2: Laravel Architecture or Figure 27: System Component Diagram]

**Speaker Notes**:
"We built this on Laravel 12 using a layered MVC architecture. We separated business logic into specific Services—like Payments and AI—to keep our controllers clean and testable."

---

## Slide 7: Database Design
*   **Structure**: Relational Database with 15 Core Tables.
*   **Key Entities**:
    *   `Users` (Single Table Inheritance for Roles).
    *   `Trades` (Complex entity with 20+ attributes).
    *   `Subscriptions` (Links Traders to Analysts).
*   **Diagram**: [Insert Figure 26: ER Diagram]

**Speaker Notes**:
"Our database is normalized to 3NF. The detailed 'Trades' table is the heart of the system, storing everything from entry price to psychological state. The 'Subscriptions' table acts as the bridge effectively linking the two main user types."

---

## Slide 8: Core Features – Trader
*   **Trade Logging**:
    *   Manual Entry Form (Technical + Psychological fields).
    *   Bulk CSV Import (Validation & Mapping).
*   **Analytics Dashboard**:
    *   Win Rate, Profit Factor, Equity Curve using Chart.js.
*   **Playbook**: Strategy tagging and performance tracking.
*   **Gamification**: XP system and Achievements (e.g., "First 100 Trades").

**Speaker Notes**:
"For the Trader, we focused on friction reduction. Import trades in bulk or log them manually. The Dashboard immediately visually renders this data, turning rows of numbers into an equity curve."

---

## Slide 9: Core Features – Analyst
*   **Analyst Dashboard**: Overview of subscribers and pending feedback.
*   **Subscriber Analysis**: Deep dive into a specific trader's history.
*   **AI-Assisted Feedback**:
    *   **Integration**: Google Gemini 2.5 Flash API.
    *   **Function**: Auto-generates draft feedback (Strengths, Weaknesses) based on trader metrics.
*   **Subscription Management**: Create plans and view revenue.

**Speaker Notes**:
"Analysts have a powerful dashboard. A standout feature used here is the AI-Assisted Feedback. We integrated Google's Gemini AI to analyze a student's metrics and draft an initial feedback report, which the analyst can then refine, saving them significant time."

---

## Slide 10: Core Features – Admin
*   **User Management**: Ban/Suspend users, Manage Roles.
*   **Analyst Verification**:
    *   Review applications (Experience, Methodology).
    *   Approve/Reject workflow.
*   **Activity Logs**: Audit trail of system actions.
*   **Backup**: Automated database backup configuration.

**Speaker Notes**:
"Trust is key. The Admin panel allows us to vet analysts before they can sell subscriptions. We also have full activity logging for security and dispute resolution."

---

## Slide 11: Payment & Subscription System
*   **Provider**: Chapa (Ethiopian Payment Gateway).
*   **Workflow**:
    1.  Trader selects Tier.
    2.  Redirect to Chapa Secure Checkout.
    3.  Payment Webhook verification.
    4.  Subscription Activated.
*   **Security**: No card data stored locally; transaction referencing only.

**Speaker Notes**:
"We integrated Chapa to support local payments. The flow is secure—we never touch the card numbers. We rely on webhooks to confirm payment and activate the subscription automatically."

---

## Slide 12: Key Workflows
*   **Trade Logging**: Entry -> Validation -> Database -> Analytics Update.
*   **Feedback Loop**:
    *   Analyst reviews Trade -> Calls AI Service (Draft) -> Edits/Sends -> Trader Notified.
*   **Diagram**: [Insert Figure 10: Submit Feedback Sequence Diagram or Figure 8: Subscribe Sequence]

**Speaker Notes**:
"Here you see the Feedback Loop. It's not just a message; it triggers the AI service, validates the subscription status, and notifies the user via email and in-app alerts."

---

## Slide 13: Security & Access Control
*   **Authentication**: Laravel Breeze + Sanctum.
*   **Role-Based Access Control (RBAC)**:
    *   Middleware (`EnsureUserHasRole`).
    *   Policies (e.g., `TradePolicy`: Users view only *their* trades).
*   **Data Privacy**:
    *   Analyst can *only* see data of *subscribed* traders.
    *   Encrypted passwords (Bcrypt).
    *   CSRF Protection.

**Speaker Notes**:
"Security is built-in, not bolted on. We use RBAC to ensure data isolation. An Analyst cannot see a Trader's journal unless that specific Trader has an active subscription."

---

## Slide 14: Testing & Validation
*   **Unit & Feature Tests**: 
    *   `AnalyticsServiceTest` (Calculation logic).
    *   `TradeTest` (CRUD operations).
*   **Manual Testing**: Verified full subscription lifecycle with Chapa sandbox.
*   **Seeder Data**: Robust factories generating 50+ trades for demo scenarios.
*   **Validation**: Strict typing and form validation requests.

**Speaker Notes**:
"We verified the system with both automated PHPUnit tests—focusing on the math in the analytics—and extensive manual testing of the payment flows using Chapa's sandbox environment."

---

## Slide 15: Challenges & Solutions
*   **Challenge**: CSV Import Complexity (Different broker formats).
    *   **Solution**: Standardized template and mapping service.
*   **Challenge**: Real-time Payment Status.
    *   **Solution**: Implemented robust Webhook listeners.
*   **Challenge**: "Cold Start" for Analysts (Writing feedback from scratch).
    *   **Solution**: Implemented Gemini AI caching service to generate drafts.

**Speaker Notes**:
"A major challenge was the 'blank page' problem for analysts. We solved this by bringing in the AI service to provide a structured starting point based on the data."

---

## Slide 16: System Limitations
*   **No Live Trading**: Users must still execute trades on MetaTrader/cTrader.
*   **Currency Support**: Primarily USD/ETB for payments; Journaling assumes single base currency.
*   **Internet Connectivity**: Required for all features (Online Web App).

**Speaker Notes**:
"We are transparent about limitations. You cannot trade from this app, and you must have an internet connection. These were scoping decisions to focus on our core competency—analytics."

---

## Slide 17: Future Enhancements
*   **Broker API Sync**: Auto-fetch trades from MetaTrader 4/5.
*   **Mobile App**: React Native wrapper for on-the-go journaling.
*   **Social Trading**: Leaderboards for top-performing traders.
*   **Advanced AI**: Chat interface for "Talk to your Journal".

**Speaker Notes**:
"The road ahead is exciting. Direct Broker sync is the next logical step to remove manual entry entirely, and a mobile app would boost engagement."

---

## Slide 18: Conclusion
*   **Achievement**: Successfully deployed a functional, secure, 3-tier platform.
*   **Impact**:
    *   Empowers retail traders with data.
    *   Creates a gig economy for trading analysts.
*   **Project Status**: Complete (Documentation, Testing, & Code).

**Speaker Notes**:
"In conclusion, we have delivered a robust, tested system that solves a real market problem. It's not just a school project; it's a viable product foundation."

---

## Slide 19: Demo Slide
*   **Functionality to Demonstrate**:
    1.  **Sign Up & Role Selection**.
    2.  **Trader Workflow**: Log a trade manually & Import CSV.
    3.  **Analytics View**: Show the equity curve updating.
    4.  **Marketplace**: Subscribe to an Analyst (Chapa Mock).
    5.  **Analyst View**: Generate AI Feedback & Send.

**Speaker Notes**:
"For the demo, I will walk you through the 'Happy Path': A user signing up, logging a winning trade, seeing their stats update, and then hiring a coach to review it."

---

## Slide 20: Thank You / Q&A
*   **Questions?**
*   **Project Repository**: [Link if applicable]
*   **Contact Info**: [Email/Phone]

**Speaker Notes**:
"Thank you for your time. We are now open for any questions regarding the architecture, implementation, or future plans."
