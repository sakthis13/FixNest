import pandas as pd
from sklearn.model_selection import train_test_split

# Load dataset
df = pd.read_csv("ai/data/seed_dataset.csv")

# Separate input and target
X = df["complaint"]
y = df["category"]

# Split into training and test sets
X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.20,
    random_state=42,
    stratify=y
)

# Create separate DataFrames
train_df = pd.DataFrame({
    "complaint": X_train,
    "category": y_train
})

test_df = pd.DataFrame({
    "complaint": X_test,
    "category": y_test
})

# Save the datasets
train_df.to_csv("ai/data/train.csv", index=False)
test_df.to_csv("ai/data/test.csv", index=False)

print("--- SPLIT COMPLETE ---")
print(f"Total examples: {len(df)}")
print(f"Training examples: {len(train_df)}")
print(f"Test examples: {len(test_df)}")

print("\n--- TRAINING CATEGORY COUNTS ---")
print(train_df["category"].value_counts())

print("\n--- TEST CATEGORY COUNTS ---")
print(test_df["category"].value_counts())