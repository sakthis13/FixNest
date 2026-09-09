import pandas as pd

# Load the dataset
df = pd.read_csv("ai/data/seed_dataset.csv")

# Basic information
print("\n--- DATASET ---")
print(df)

# Number of rows and columns
print("\n--- SHAPE ---")
print(df.shape)

# Column names
print("\n--- COLUMNS ---")
print(df.columns.tolist())

# Number of examples in each category
print("\n--- CATEGORY COUNTS ---")
print(df["category"].value_counts())

# Number of unique categories
print("\n--- UNIQUE CATEGORIES ---")
print(df["category"].nunique())

# Check for duplicate complaints
print("\n--- DUPLICATE COMPLAINTS ---")
print(df["complaint"].duplicated().sum())

# Check for missing values
print("\n--- MISSING VALUES ---")
print(df.isnull().sum())