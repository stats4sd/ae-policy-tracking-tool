# ae-policy-tracking-tool
Proof of Concept Application for policy tracking

## Proof of Concept Spec

### Key ‘lookup’ items in the database

- CFS Policy Recommendations: 5 entries;
    - A policy recommendation can be linked to many AE Principles
- AE Principles
    - AE Principles can be linked to many CFS Policy Recommendations
- Priority Actions (23 suggested priority action areas)
    - A CFS policy has many Priority Actions, and a priority action is linked to a single CFS Policy

---

### Main Assessment

Each country / political entity has an entry in the “countries” table. 

A country has many “assessments” 

- Each assessment has the following components:
    - For each of the 24 **priority actions:**
        - A general “status”;
        - “Measures creating perverse incentives”
        - “Measures that go beyond policy recommendations”
        - “View from a Civil Society perspective”

A user can add *any number of statements* to each of the 4 x 24 components. Each statement has 

- a free-text entry,
- the ability to add one or more pieces of “evidence”.
    - Evidence = e.g.
        - file uploads
        - (text) references to policy documents. (e.g. “The Cattle Grazing Act (CAP 42)” )
        - (text) other references.
        - Probably, there should be a multi-upload for files, a free-text field, and a toggle or checkbox to state whether the evidence comes from official policy documents or other sources.
- Statements can also be linked to 1 or more AE Principles.
    
    

is drafted, then marked as “ready for review” (i.e. finalised.), and the assessment is timestamped.

- After being marked as ‘ready for review’, any changes to the assessment must be made on a copy.

---

### User Management

Our standard setup of inviting users to teams and or roles (to be added later)
