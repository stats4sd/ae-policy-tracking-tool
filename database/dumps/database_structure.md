## Database Structure Notes


### Main Tables

- **ae_principles**: The 13 AE Principles. Currently only used to tag recommendations. Potentially can be used in the future to explore how well policies align with all the AE Principles.

- **assessments**: The main table for assessments. There may be multiple assessments per country, e.g. run at different times or by different organizations.
- **countries**: List of countries. Each assessment is linked to a country.
- **highlights**: Highlights / quotations from policy documents. These are created either thorugh the automated search functionality or manually by users.
   - _automatic: boolean field to indicate if the highlight was created automatically through search or manually by a user._
   - _verified: boolean field to indicate if the highlight has been verified by a user._This should include automatic highlights that have been reviewed and confirmed by a user, **and** manual highlights created by users.
- **policy_documents**: The policy documents that are assessed. Each document is linked to an assessment. The full document text is also stored in the table to allow for automated processing (e.g. search), and for rendering on the front-end in a format that can be highlighted.
- **priority_actions**: The priority actions that are based on the CFS Policy Recommendations. Highlighted quotes and summary statements are linked to one or more priority actions, and much of the analysis is based on how well the policy documents address these priority actions.
- **programs**: Programs or organizations that run assessments. This is currently un-used, but may be used in the future, e.g. to group assessments by program/organization, and enable users from each organisation to only access their own assessments.
- **recommendations**: The 5 CFS Policy Recommendations. Each recommendation is linked to one or more priority actions, and to one or more AE Principles.
- **search_terms**: The search terms used to automatically identify relevant text in policy documents. These are linked to priority actions, so that each priority action has a set of search terms associated with it.
- **statements**: Summary statements that are linked to one or more highlights. These statements are used to summarise the findings for each priority action. 
    - This structure is likely to change very soon, to more closely match the analysis process used in the pilot testing.
- **types**: The types of "statement". There are currently 4 types, based on the original drafts of the assessment process.  This will change to reflect the new structure of the analysis process.



### Link (Pivot) Tables
Tables that link two main tables together to create many-to-many relationships.

- **ae_principle_recommendation**: Links AE Principles to Recommendations.
- **ae_principle_statement**: Currently un-used; probably will be deleted.
- **assessment_priority_action**: Currently un-used; probably will be deleted.
- **assessment_priority_action_policy**: Currently un-used; probably will be deleted.
- 
**highlight_priority_action**
**highlight_search_term**
**highlight_statement**
**policy_document_statement**

**program_team**

 - 

**program_user**


### System Tables
Tables required by the system for various functions (e.g. user roles and permissions, job queues, caching, etc.). These are not directly relevant to the main 'data' of the application, but are necessary for its operation.

- **assessment_invites**: Used for inviting users to join assessments
- **assessment_user**: Links users to assessments they have access to.
- **cache**: Laravel cache table.
- **cache_locks**: Laravel cache locks table.
- **failed_jobs**: Laravel failed jobs table.
- **invites**: Used for inviting new users to the system.
- **job_batches**: Laravel job batches table.
- **jobs**: Laravel jobs table.
- **media**: Laravel media library table. Used for storing files uploaded to the system.
- **migrations**: Laravel migrations table.
- **model_has_permissions**: Links models (e.g. users) to permissions.
- **model_has_roles**: Links models (e.g. users) to roles.
- **password_reset_tokens**: Laravel password reset tokens table.
- **permissions**: Laravel permissions table.
- **role_has_permissions**: Links roles to permissions.
- **roles**: Laravel user roles table.
- **sessions**: Laravel sessions table.
- **team_members**: Links users to teams.
- **teams**: Laravel teams table.
- **users**: Laravel users table.
- **telescope_entries**: Laravel Telescope table for debugging and monitoring
- **telescope_entries_tags**: Laravel Telescope tags table
- **telescope_monitoring**: Laravel Telescope monitoring table
